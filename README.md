# PostCard

> _Del din oplevelse – ikke hele dit liv._

PostCard is a private, group-based photo sharing web application for small groups of people who know each other: families, classmates, colleagues, sports clubs and friends.

Instead of a public feed built around followers and endless scrolling, PostCard works like a shared digital travel album. Each group is closed and independent, and a post shared in one group is never visible in another.

Semester project: **Web Programming** and **Backend & Databases**.

## Key features

- Registration and login with email and password
- A personal front page with trending and sticky posts, the newest comments and your groups. Everything comes only from your own groups.
- Groups created by any user and joined through invitation links
- **Experiences** (e.g. a trip), each collecting the posts from one period
- Posts with up to 5 photos, a caption, a date and a location
- Two posting modes per group:
  - **Open posting:** members create experiences freely
  - **Permission-based posting:** a member asks the group admin to approve an experience with dates and a post limit
- Comments and likes on posts, including old posts in the archive
- Two admin roles:
  - **Group Admin:** manages members, invitations and posting permissions for their own group
  - **Platform Admin:** a protected backend for site content, users, posts and blocking/banning

## Tech stack

The whole application is built from scratch, **without any framework or CMS**.

| Layer           | Technology                                           |
| --------------- | ---------------------------------------------------- |
| Backend         | Vanilla PHP 8 (object-oriented), no framework        |
| Database        | MySQL / MariaDB, normalised to 3NF, hand-written SQL |
| Frontend        | Semantic HTML, hand-written CSS, vanilla JavaScript  |
| Local server    | WampServer (Apache + PHP + MySQL/MariaDB)            |
| Version control | Git + GitHub                                         |

## Folder structure

```text
PostCard/
├── public/              Web root: the only folder the browser can reach
│   ├── index.php        Front controller: every request enters here
│   ├── assets/          CSS (main, components, pages), JS, images
│   └── uploads/         User media (not committed)
├── app/
│   ├── Controllers/     Handle requests and choose what happens next
│   ├── Models/          Data objects (User, Group, Post …)
│   ├── Repositories/    All database queries (prepared statements)
│   ├── Services/        Business rules (e.g. may this user post now?)
│   └── Helpers/         Small shared functions (escaping, CSRF, URLs)
├── views/               Presentation only: layouts, partials, pages
├── config/              Application configuration (reads from .env)
├── database/            schema.sql, seed.sql and database documentation
├── docs/                Project documentation and report evidence
├── tests/               Tests
└── .env.example         Template for local environment settings
```

Presentation (`views/`) is kept separate from application logic (`app/`). Only `public/` is exposed to the browser, so configuration, source code and SQL files cannot be requested directly.

## Getting started (local)

1. Copy `.env.example` to `.env` and fill in your local database settings.
2. Create the database by running `database/schema.sql` and then `database/seed.sql`.
3. Open the site in the browser. The web root is the `public/` folder.

More detailed setup steps will be added as the project develops.

## Security principles

- Passwords are hashed with PHP's `password_hash()` / `password_verify()`
- All SQL uses prepared statements
- All output is escaped to prevent XSS
- CSRF tokens on every form that changes data
- Hardened sessions and an authorisation check on every protected page
- Strict group isolation: users can only access groups they are members of
- Uploads are validated by type, size and filename, and executable files are rejected
- Secrets live in `.env`, which is never committed

## Accessibility

- Semantic HTML with a correct heading hierarchy
- Every form field has a visible label and clear error messages
- Full keyboard navigation with visible focus states
- Sufficient colour contrast, and status messages that don't rely on colour alone
- Responsive, mobile-first layout with touch-friendly controls
- Danish user interface

## Team

Web Programming / Backend & Databases semester project.
