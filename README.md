# Gift Exchange

A Laravel-based Secret Santa organizer that makes gift exchanges simple and fun. Create events, invite participants, and automatically draw names with exclusions support.

## Features

- **No accounts required** - Token-based access for organizers and participants
- **Automatic name drawing** - Creates circular assignments ensuring everyone gives and receives
- **Exclusions support** - Prevent specific participants from being matched
- **Wish lists** - Participants can share interests to help their Secret Santa
- **Interactive reveal** - Fun spinning wheel animation to reveal assignments
- **Event management** - Set event dates, themes, and gift amount limits

## How It Works

1. **Create an event** - Set up your gift exchange with details and preferences
2. **Add participants** - Each participant receives a unique access link
3. **Enter interests** - Participants share their wish lists
4. **Automatic drawing** - Once everyone is ready, names are drawn automatically
5. **View assignments** - Each participant sees who they're giving to via their unique link

## Tech Stack

- Laravel 12
- PHP 8.4
- SQLite (default)
- Tailwind CSS 4
- Alpine.js 3
- Pest (testing)
