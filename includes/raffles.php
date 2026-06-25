<?php
declare(strict_types=1);

function raffle_find(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM raffles WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $raffle = $stmt->fetch();
    return $raffle ?: null;
}

function raffle_public_find(int $id): ?array
{
    $stmt = db()->prepare("SELECT * FROM raffles WHERE id = ? AND status IN ('active', 'finished') LIMIT 1");
    $stmt->execute([$id]);
    $raffle = $stmt->fetch();
    return $raffle ?: null;
}

function raffle_numbers(int $raffleId): array
{
    $stmt = db()->prepare('SELECT * FROM raffle_numbers WHERE raffle_id = ? ORDER BY number_value ASC');
    $stmt->execute([$raffleId]);
    return $stmt->fetchAll();
}

function raffle_number_find(int $raffleId, int $number): ?array
{
    $stmt = db()->prepare('SELECT * FROM raffle_numbers WHERE raffle_id = ? AND number_value = ? LIMIT 1');
    $stmt->execute([$raffleId, $number]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function raffle_stats(int $raffleId): array
{
    $stmt = db()->prepare(
        "SELECT
            COUNT(*) total,
            SUM(status = 'available') available,
            SUM(status = 'reserved') reserved,
            SUM(status = 'sold') sold
        FROM raffle_numbers WHERE raffle_id = ?"
    );
    $stmt->execute([$raffleId]);
    $stats = $stmt->fetch() ?: ['total' => 0, 'available' => 0, 'reserved' => 0, 'sold' => 0];
    foreach ($stats as $key => $value) {
        $stats[$key] = (int) $value;
    }
    return $stats;
}

function ensure_numbers_for_raffle(int $raffleId, int $quantity): void
{
    $pdo = db();
    $stmt = $pdo->prepare('INSERT IGNORE INTO raffle_numbers (raffle_id, number_value, status) VALUES (?, ?, "available")');
    for ($i = 1; $i <= $quantity; $i++) {
        $stmt->execute([$raffleId, $i]);
    }
    $delete = $pdo->prepare('DELETE FROM raffle_numbers WHERE raffle_id = ? AND number_value > ? AND status = "available"');
    $delete->execute([$raffleId, $quantity]);
}

function audit_number_change(int $numberId, int $userId, string $action, array $before, array $after): void
{
    $stmt = db()->prepare(
        'INSERT INTO number_audits (raffle_number_id, user_id, action, old_data, new_data, created_at)
         VALUES (?, ?, ?, ?, ?, NOW())'
    );
    $stmt->execute([
        $numberId,
        $userId,
        $action,
        json_encode($before, JSON_UNESCAPED_UNICODE),
        json_encode($after, JSON_UNESCAPED_UNICODE),
    ]);
}

function sell_raffle_number(int $raffleId, int $numberId, int $userId, array $data): array
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare('SELECT * FROM raffle_numbers WHERE id = ? AND raffle_id = ? LIMIT 1 FOR UPDATE');
        $stmt->execute([$numberId, $raffleId]);
        $before = $stmt->fetch();

        if (!$before) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Número no encontrado.'];
        }

        if ($before['status'] === 'sold') {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Este número ya fue vendido.'];
        }

        $after = [
            'status' => 'sold',
            'buyer_name' => clean_string((string) ($data['buyer_name'] ?? ''), 140),
            'buyer_phone' => clean_phone((string) ($data['buyer_phone'] ?? ''), 40),
            'buyer_city' => clean_string((string) ($data['buyer_city'] ?? ''), 100),
            'notes' => clean_string((string) ($data['notes'] ?? ''), 1000),
            'registered_at' => date('Y-m-d H:i:s'),
            'registered_by' => $userId,
        ];

        if ($after['buyer_name'] === '') {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Ingresa el nombre del comprador.'];
        }

        $update = $pdo->prepare(
            'UPDATE raffle_numbers
             SET status = "sold", buyer_name = ?, buyer_phone = ?, buyer_city = ?, notes = ?, registered_at = ?, registered_by = ?
             WHERE id = ? AND raffle_id = ? AND status <> "sold"'
        );
        $update->execute([
            $after['buyer_name'],
            $after['buyer_phone'] ?: null,
            $after['buyer_city'] ?: null,
            $after['notes'] ?: null,
            $after['registered_at'],
            $after['registered_by'],
            $numberId,
            $raffleId,
        ]);

        if ($update->rowCount() !== 1) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'No se pudo vender el número porque cambió de estado.'];
        }

        audit_number_change($numberId, $userId, 'Venta de número', $before, $after);
        $pdo->commit();

        return ['ok' => true, 'number_value' => (int) $before['number_value']];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}

function update_raffle_number_admin(int $raffleId, int $numberId, int $userId, array $data): array
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare('SELECT * FROM raffle_numbers WHERE id = ? AND raffle_id = ? LIMIT 1 FOR UPDATE');
        $stmt->execute([$numberId, $raffleId]);
        $before = $stmt->fetch();

        if (!$before) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Número no encontrado.'];
        }

        $status = in_array($data['status'] ?? '', ['available', 'reserved', 'sold'], true) ? $data['status'] : 'available';
        if ($before['status'] === 'sold' && $status === 'sold') {
            $registeredAt = $before['registered_at'];
        } elseif ($status === 'available') {
            $registeredAt = null;
        } else {
            $registeredAt = date('Y-m-d H:i:s');
        }

        $after = [
            'status' => $status,
            'buyer_name' => clean_string((string) ($data['buyer_name'] ?? ''), 140),
            'buyer_phone' => clean_phone((string) ($data['buyer_phone'] ?? ''), 40),
            'buyer_city' => clean_string((string) ($data['buyer_city'] ?? ''), 100),
            'notes' => clean_string((string) ($data['notes'] ?? ''), 1000),
            'registered_at' => $registeredAt,
            'registered_by' => $status === 'available' ? null : $userId,
        ];

        if ($status === 'available') {
            $after['buyer_name'] = null;
            $after['buyer_phone'] = null;
            $after['buyer_city'] = null;
            $after['notes'] = null;
        }

        if ($status === 'sold' && empty($after['buyer_name'])) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Ingresa el nombre del comprador para vender.'];
        }

        $update = $pdo->prepare(
            'UPDATE raffle_numbers
             SET status=?, buyer_name=?, buyer_phone=?, buyer_city=?, notes=?, registered_at=?, registered_by=?
             WHERE id=? AND raffle_id=?'
        );
        $update->execute([
            $after['status'],
            $after['buyer_name'],
            $after['buyer_phone'],
            $after['buyer_city'],
            $after['notes'],
            $after['registered_at'],
            $after['registered_by'],
            $numberId,
            $raffleId,
        ]);
        audit_number_change($numberId, $userId, 'Actualización administrativa del número', $before, $after);
        $pdo->commit();

        return ['ok' => true, 'number_value' => (int) $before['number_value']];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
