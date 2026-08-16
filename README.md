# CRM Leads API

Laravel REST API для лідів і дзвінків.

```bash
make build && make up && make fresh   # http://localhost:8080
make test
```

## Бізнес-логіка

- Контролери в `app/Http/Controllers`.
- Валідація в `app/Http/Requests`, відповіді в `app/Http/Resources`.
- Уся бізнес-логіка в `app/Services`: `LeadStatusService` призначає менеджера і змінює статус ліда, `CallService` створює дзвінок у транзакції.
- Запускає її `CallObserver` (`app/Observers`) одразу після створення дзвінка.
- Усі правила статусів зібрані в одному сервісі, тож є одне місце для змін.
- Статуси і результати дзвінків - енами в `app/Enums`, схема - `database/migrations`.
- Тести в `tests/Feature`.

## Що можна покращити

- Пагінація для списку лідів менеджера замість `get()`.
- Кеш для списку лідів і агрегатів по дзвінках.
- Rate limiting на API.
- Важкі побічні дії після дзвінка винести у черги.
- Індекси під реальні запити.
- Юніт-тести на крайні випадки `LeadStatusService`.


