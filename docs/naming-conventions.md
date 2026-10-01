# PostCard — Naming Conventions

These rules are shared by the whole team. When you're unsure, look at how existing code does it and do the same.

**Language rule:** code, file names, database names and comments are in **English**. Everything the user sees on screen is in **Danish**.

---

## 1. Files and folders

| What | Style | Example |
|---|---|---|
| Folders | lowercase, or PascalCase for class folders | `views/partials/`, `app/Controllers/` |
| PHP class files | PascalCase, same name as the class | `GroupController.php`, `PostRepository.php` |
| View files (pages, partials) | kebab-case | `group-detail.php`, `post-card.php` |
| CSS files | kebab-case | `main.css`, `components/post-card.css` |
| JS files | kebab-case, one feature per file | `photo-preview.js` |
| Images | kebab-case, descriptive | `hero-santorini.webp`, `stamp-airmail.svg` |
| SQL files | snake_case | `schema.sql`, `seed.sql` |

Never use spaces, æ/ø/å or capital letters in view, CSS, JS or image file names. Some servers treat `Hero.jpg` and `hero.jpg` as different files.

---

## 2. PHP

We follow **PSR-1 and PSR-12**, the standard PHP style guides.

| What | Style | Example |
|---|---|---|
| Namespace | `PostCard\` + folder name | `namespace PostCard\Repositories;` |
| Class | PascalCase, a noun | `PostRepository`, `PermissionService` |
| Interface | PascalCase + `Interface` | `RepositoryInterface` |
| Method | camelCase, starts with a verb | `findByGroup()`, `canPost()`, `approveRequest()` |
| Variable / property | camelCase | `$groupId`, `$remainingPosts` |
| Constant | UPPER_SNAKE_CASE | `MAX_PHOTOS_PER_POST = 5` |
| Boolean | starts with `is`, `has` or `can` | `$isAdmin`, `hasPermission()`, `canPost()` |
| Helper function | camelCase, short | `url()`, `csrfField()`. The one exception is `e()` for escaping. |

**Class suffixes show a class's job:**

| Suffix | Job | Example |
|---|---|---|
| `Controller` | Receives the request and chooses a view | `GroupController` |
| `Repository` | Runs SQL. It's the only place with SQL. | `GroupRepository` |
| `Service` | Business rules | `PermissionService` |
| (none) | Model / data object | `User`, `Group`, `Post` |

**Method name prefixes:**

| Prefix | Meaning | Returns |
|---|---|---|
| `find…` | Fetch, may be empty | object or `null` / an array |
| `get…` | Fetch, must exist | a value (or throws an error) |
| `create…`, `update…`, `delete…` | Change data | `bool` or a new id |
| `is…`, `has…`, `can…` | Yes/no question | `bool` |

---

## 3. HTML and CSS

**CSS classes use BEM** (Block, Element, Modifier), as in Salon Zig Zag:

```text
.block               → .post-card
.block__element      → .post-card__photo, .post-card__caption
.block--modifier     → .post-card--sticky, .button--secondary
```

- Class names are kebab-case and in English.
- A `<ul>` with a class has no bullets or indent (reset in `main.css`); a plain `<ol>`/`<ul>` in text keeps them.
- Style with **classes only**, never with an `id` or a bare element selector inside a component.
- `id` is only for linking a `<label for="">`, `aria-controls` or anchor links. Use kebab-case: `id="post-caption"`.
- State classes start with `is-`: `.is-open`, `.is-active`. JavaScript switches these on and off.
- JavaScript hooks start with `js-` and are never styled: `.js-photo-input`.
- Utility classes do one small job and have no BEM parts: `.container`, `.flow` (even spacing between children), `.visually-hidden`, `.eyebrow`, `.brand`, `.meta` (small grey text), `.group-label`, `.stretched-link` (makes a whole card clickable).
- **The site name is always shown in capitals.** In running text write `<span class="brand">PostCard</span>`: CSS shows it as POSTCARD, and screen readers still read it as a word. Where CSS can't reach (e.g. `<title>`), write `POSTCARD`.

**CSS custom properties (design tokens):**

| Group | Pattern | Example |
|---|---|---|
| Brand colour | `--color-<name>` | `--color-ink`, `--color-terracotta` |
| Colour by role | `--color-<role>` | `--color-text`, `--color-bg`, `--color-border` |
| Font | `--font-<role>` | `--font-heading`, `--font-hand` |
| Font size | `--text-<size>` | `--text-h1`, `--text-small` |
| Spacing | `--space-<size>` | `--space-xs` … `--space-xl` |
| Other | `--radius-*`, `--shadow-*` | `--radius-card`, `--shadow-card` |

Components use the **role** colours (`--color-text`), not brand colours (`--color-ink`). Then a colour change only has to happen in one place.

---

## 4. JavaScript

| What | Style | Example |
|---|---|---|
| Variable / function | camelCase | `photoInput`, `showPreview()` |
| Constant | UPPER_SNAKE_CASE | `MAX_PHOTOS` |
| DOM element variable | describes the element | `menuButton`, `previewList` |
| Event handler | `handle` + event | `handleFileChange()` |

---

## 5. Database (SQL)

> Matches the team's ER design, "Postkort tables v2 (standard naming)" in draw.io.

| What | Style | Example |
|---|---|---|
| Table | snake_case, plural | `users`, `experiences`, `post_images` |
| Reserved words | Never use one as a name; add a prefix | `user_groups`, because `GROUPS` is reserved in MySQL 8 |
| Column | snake_case | `first_name`, `created_at` |
| Primary key | `<singular>_id` | `user_id`, `group_id`, `experience_id` |
| Foreign key | same name as the primary key it points to | `posts.experience_id` → `experiences.experience_id` |
| Status column | `<table singular>_status`, `ENUM` | `user_status`, `member_status`, `experience_status` |
| Role column | `<table singular>_role`, `ENUM` | `member_role` |
| Boolean column | `is_` / `has_`, `TINYINT(1)` | `is_operator`, `is_sticky` |
| Date/time | `_at` for a moment (`DATETIME`), `_date` or `date` for a date only (`DATE`) | `created_at`, `start_date`, `birthdate` |
| Link table (many-to-many) | both names, or what the link means | `group_members`, `likes` |
| Link table primary key | composite key made of both foreign keys | `PRIMARY KEY (user_id, post_id)` |
| View | `v_` + what it shows | `v_group_feed`, `v_post_stats` |
| Trigger | `trg_<table>_<before/after>_<event>` | `trg_posts_after_insert` |
| Index | `idx_<table>_<columns>` | `idx_experiences_group_created` |
| Unique constraint | `uq_<table>_<columns>` | `uq_users_email` |
| Foreign key constraint | `fk_<table>_<referenced table>` | `fk_posts_experiences` |

SQL keywords are written in UPPERCASE and names in lowercase: `SELECT title FROM experiences WHERE group_id = ?`.

---

## 6. URLs

- English, lowercase, kebab-case, **plural resources**. This matches the team sitemap: `/groups/12`, `/experiences/create`, `/forgot-password`.
- Operator pages all start with `/admin/`.

---

## 7. Git

| What | Style | Example |
|---|---|---|
| Branch | `<type>/<jira-key>-<short-description>` | `feature/POST-21-design-tokens`, `fix/POST-40-login-error` |
| Commit | Imperative mood, English, starts with a verb, Jira key at the end | `Add design tokens and base styles (POST-21)` |

Types: `feature`, `fix`, `docs`, `refactor`, `db`.
