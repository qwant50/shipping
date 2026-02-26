CONSOLE COMMANDS
----------------

All commands must be run inside the `app` Docker container:

```bash
docker compose exec app php yii <command>
```

### `guest` — Print all guests

Prints each guest with their bookings and accounts.

```bash
docker compose exec app php yii guest
```

Example output:

```
guest_id: 177
guest_type: crew
first_name: Marco
last_name: Burns
gender: M
guest_booking:
  0:
    booking_number: 20008683
    ship_code: OST
    room_no: A0073
    ...
```

### `sort` — Print guests sorted by field(s)

Sorts the guest list (and nested sub-lists) by one or more fields.

```bash
# Single field, ascending (default)
docker compose exec app php yii sort --sort=last_name

# Single field, descending
docker compose exec app php yii sort --sort=last_name:desc

# Multiple fields
docker compose exec app php yii sort --sort=last_name:asc,account_id:desc
```

**Sort spec format:** `field:direction` separated by commas. Direction is `asc` or `desc` (case-insensitive); omitting it defaults to `asc`.

Sorting is applied recursively — nested lists (e.g. `guest_booking`, `guest_account`) are sorted by the same key set if the key exists at that depth.

