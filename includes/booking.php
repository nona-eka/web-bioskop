<?php
declare(strict_types=1);

function fetch_schedule(PDO $pdo, int $scheduleId): ?array
{
    $stmt = $pdo->prepare('
        SELECT s.*, m.title, m.genre, m.poster, m.duration,
               st.capacity, st.name AS studio_name, c.name AS cinema_name
        FROM schedules s
        JOIN movies m ON s.movie_id = m.id
        JOIN studios st ON s.studio_id = st.id
        JOIN cinemas c ON st.cinema_id = c.id
        WHERE s.id = :id
    ');
    $stmt->execute([':id' => $scheduleId]);

    return $stmt->fetch() ?: null;
}

function seat_layout(int $capacity): array
{
    $rows = [];
    $remaining = $capacity;

    foreach (range('A', 'Z') as $letter) {
        if ($remaining <= 0) {
            break;
        }

        $count = min(SEATS_PER_ROW, $remaining);
        $rows[$letter] = array_map(static fn (int $number): string => $letter . $number, range(1, $count));
        $remaining -= $count;
    }

    return $rows;
}

function booked_seats(PDO $pdo, int $scheduleId): array
{
    $stmt = $pdo->prepare('
        SELECT bs.seat_number
        FROM booking_seats bs
        JOIN bookings b ON bs.booking_id = b.id
        WHERE b.schedule_id = :schedule_id AND b.payment_status <> \'cancelled\'
    ');
    $stmt->execute([':schedule_id' => $scheduleId]);

    return array_map('strtoupper', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function sanitize_seats(mixed $input, int $capacity): array
{
    if (!is_array($input)) {
        return [];
    }

    $validSeats = array_merge(...array_values(seat_layout($capacity)));
    $clean = [];

    foreach ($input as $seat) {
        if (!is_string($seat)) {
            continue;
        }

        $seat = strtoupper(trim($seat));

        if (in_array($seat, $validSeats, true)) {
            $clean[$seat] = $seat;
        }
    }

    $seats = array_values($clean);
    usort($seats, 'strnatcmp');

    return $seats;
}

function sanitize_quantities(mixed $input): array
{
    if (!is_array($input)) {
        return [];
    }

    $quantities = [];

    foreach ($input as $productId => $quantity) {
        $productId = positive_int($productId);
        $quantity = positive_int($quantity);

        if ($productId !== null && $quantity !== null) {
            $quantities[$productId] = min($quantity, MAX_QUANTITY_PER_PRODUCT);
        }
    }

    return $quantities;
}

function resolve_products(PDO $pdo, array $quantities): array
{
    if (!$quantities) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($quantities), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, category FROM products WHERE id IN ($placeholders) ORDER BY category, name");
    $stmt->execute(array_keys($quantities));

    $items = [];

    foreach ($stmt->fetchAll() as $product) {
        $quantity = $quantities[(int) $product['id']];
        $items[] = [
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => (float) $product['price'],
            'quantity' => $quantity,
            'subtotal' => (float) $product['price'] * $quantity,
        ];
    }

    return $items;
}

function order_totals(array $schedule, array $seats, array $items): array
{
    $ticketTotal = (float) $schedule['price'] * count($seats);
    $fnvTotal = array_sum(array_column($items, 'subtotal'));

    return [
        'ticket' => $ticketTotal,
        'fnv' => (float) $fnvTotal,
        'grand' => $ticketTotal + $fnvTotal,
    ];
}

function get_draft(): ?array
{
    $draft = $_SESSION['booking_draft'] ?? null;

    return is_array($draft) ? $draft : null;
}

function set_draft(int $scheduleId, array $seats, array $quantities = []): void
{
    $_SESSION['booking_draft'] = [
        'schedule_id' => $scheduleId,
        'seats' => $seats,
        'quantities' => $quantities,
    ];
}

function clear_draft(): void
{
    unset($_SESSION['booking_draft']);
}

function load_valid_draft(PDO $pdo): array
{
    $draft = get_draft();

    if ($draft === null) {
        redirect('movies.php');
    }

    $schedule = fetch_schedule($pdo, (int) $draft['schedule_id']);

    if ($schedule === null) {
        clear_draft();
        flash_set('error', 'Jadwal tidak ditemukan.');
        redirect('movies.php');
    }

    $seats = sanitize_seats($draft['seats'], (int) $schedule['capacity']);

    if (!$seats || count($seats) > MAX_SEATS_PER_BOOKING) {
        flash_set('error', 'Pilih 1 sampai ' . MAX_SEATS_PER_BOOKING . ' kursi terlebih dahulu.');
        redirect('seat-selection.php?schedule_id=' . $schedule['id']);
    }

    $taken = array_intersect($seats, booked_seats($pdo, (int) $schedule['id']));

    if ($taken) {
        flash_set('error', 'Kursi ' . implode(', ', $taken) . ' sudah dipesan orang lain. Silakan pilih kursi lain.');
        redirect('seat-selection.php?schedule_id=' . $schedule['id']);
    }

    return [$schedule, $seats, sanitize_quantities($draft['quantities'])];
}
