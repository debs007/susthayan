# Pharmacy Platform - Backend Foundation

Generated from your System Flow + SRS documents, built up over many
passes. Every flow from the System Flow document now has real, working
code behind it - schema, Auth, Customer Orders, Purchase & Vendor, POS,
Delivery, Franchise Settlement, Admin Panel, and Reporting/Accounting.
Each module's own section below documents what's real vs. deliberately
simplified within it - see "Not included yet" at the end for the running
list of what's still open across the whole app.

## 1. Bootstrap a real Laravel 13 project

This was written outside a live Laravel install (no internet access in the
environment that generated it), so start a real project first, then drop
these files in on top:

```bash
composer create-project laravel/laravel:^13.0 pharmacy-platform
cd pharmacy-platform
php artisan install:api          # scaffolds Sanctum + routes/api.php
```

Then copy in everything from this package, overwriting the defaults:

```bash
cp -r database/migrations/* <your-project>/database/migrations/
cp -r database/seeders/* <your-project>/database/seeders/
cp -r database/factories/* <your-project>/database/factories/
cp -r app/Models/* <your-project>/app/Models/
cp -r app/Enums <your-project>/app/
cp -r app/Events <your-project>/app/
cp -r app/Exceptions/*.php <your-project>/app/Exceptions/
cp -r app/Services <your-project>/app/
cp -r app/Http/Controllers/Api <your-project>/app/Http/Controllers/
cp -r app/Http/Controllers/Web <your-project>/app/Http/Controllers/
cp -r app/Http/Requests <your-project>/app/Http/
cp -r app/Http/Resources <your-project>/app/Http/
cp -r app/Http/Middleware/EnsureFranchiseAccess.php <your-project>/app/Http/Middleware/
cp app/Providers/AppServiceProvider.php <your-project>/app/Providers/AppServiceProvider.php
cp bootstrap/app.php <your-project>/bootstrap/app.php
cp config/auth.php <your-project>/config/auth.php
cp config/services.php <your-project>/config/services.php
cp routes/api.php <your-project>/routes/api.php
cp routes/web.php <your-project>/routes/web.php
cp routes/channels.php <your-project>/routes/channels.php
cp -r resources/views/* <your-project>/resources/views/
cp resources/css/app.css <your-project>/resources/css/app.css
cp resources/js/app.js <your-project>/resources/js/app.js
cp package.json <your-project>/package.json
cp vite.config.js <your-project>/vite.config.js
cp composer.json <your-project>/composer.json     # or merge the require block manually
cp .env.example <your-project>/.env.example
```

## 2. Install dependencies and configure

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan install:broadcasting   # generates config/broadcasting.php + config/reverb.php
```

`install:broadcasting` shells out to `npm install --save-dev laravel-echo pusher-js
--ignore-scripts && npm run build` internally, through Laravel's `Process` facade -
which kills anything over 60 seconds by default. On a slow connection or a
first-time npm cache, that combined install+build can genuinely take
longer than that, and you'll see a `ProcessTimedOutException` even though
nothing is actually broken. If that happens: check whether
`config/broadcasting.php` / `config/reverb.php` / the `REVERB_*` lines in
`.env` already exist (config publishing happens before the npm step, so
it usually already succeeded), then just finish the npm part yourself -
`npm install --save-dev laravel-echo pusher-js && npm run build` - which
has no such timeout when run directly in your own terminal.

`bootstrap/app.php` already wires `routes/channels.php` in manually (see
below for why), so if `install:broadcasting` complains about that line
already being there or tries to duplicate it, just keep the version from
this package - it's already correct.

Fill in `.env`: MySQL credentials, Redis, `REVERB_*`, and
`RAZORPAY_KEY_ID` / `RAZORPAY_KEY_SECRET` / `RAZORPAY_WEBHOOK_SECRET`.
Needs the `pdo_mysql` PHP extension enabled (on by default in most PHP 8.4
installs - `php -m | grep pdo_mysql` to check).

## Permissions

Laravel only ever *writes* to two places at runtime: `storage/` (logs,
cached views/sessions, and in this app, uploaded prescriptions and proof-
of-delivery files) and `bootstrap/cache/` (compiled config/route caches).
Everything else just needs to be readable.

```bash
# 1. Make sure you (or whoever runs php/artisan) actually own the project.
#    This is the step people skip, and it's the usual reason "I set 755
#    and it still fails" happens - 755 on a directory only grants write
#    access to its OWNER. If PHP runs as a different user than whoever
#    extracted/created these files, no permission number fixes that alone.
sudo chown -R $USER:$USER .

# 2. Directories 755, files 644, everywhere except vendor/ and
#    node_modules/ - composer/npm manage those, and a blanket chmod over
#    them can strip the execute bit off binaries like vendor/bin/pint.
find . \( -path ./vendor -o -path ./node_modules -o -path ./.git \) -prune \
  -o -type d -exec chmod 755 {} \;
find . \( -path ./vendor -o -path ./node_modules -o -path ./.git \) -prune \
  -o -type f -exec chmod 644 {} \;
```

**If `storage/` or `bootstrap/cache/` still can't be written to after
that**, it's almost always because a *web server* (nginx, Apache, PHP-FPM)
is running as a different user than the one you just `chown`-ed to -
`php artisan serve` for local dev doesn't have this problem since it runs
as you, but a real Apache/Nginx setup usually runs PHP as `www-data` (or
similar). Check with:

```bash
ps aux | grep -E 'php-fpm|nginx|apache' | head -5
```

If that shows a different user, either add yourself to that user's group
and make storage/bootstrap-cache group-writable (`sudo chmod -R 775
storage bootstrap/cache` + `sudo usermod -aG www-data $USER`), or `chown`
those two directories to that user directly. **Don't reach for `chmod
-R 777`** as a quick fix - it makes those directories writable by every
user on the system, which is a real security problem, not just a messy
one, for directories holding uploaded prescriptions.

One more possibility if you're on RHEL/CentOS/Fedora/Amazon Linux:
SELinux can block writes even with fully correct Unix permissions and
ownership. If `ls -laZ storage` shows a context that doesn't look like
`httpd_sys_rw_content_t` (or equivalent) and nothing above has fixed it,
that's worth checking next.

## 3. Migrate and seed

```bash
php artisan migrate
php artisan db:seed
```

(`db:seed` alone now runs both `RolePermissionSeeder` and creates the local
Super Admin - see below.)

## 4. Run it

```bash
php artisan serve         # backend at http://localhost:8000
npm run dev                # Vite dev server for the web portal's CSS/JS
php artisan reverb:start   # WebSocket server - without this, nothing broadcasts
php artisan queue:work     # processes queued jobs, INCLUDING every broadcast
```

That last one is easy to miss and worth being explicit about: `OrderStatusUpdated`
and `NewOrderPlaced` both implement `ShouldBroadcast`, which Laravel queues
by default rather than sending inline. Skip `queue:work` and those events
sit in the queue forever - not erroring, just silently never reaching
anyone. Same applies to anywhere else a queued job gets added later.

Visit `http://localhost:8000/login` for the Franchise/Admin web portal.
`/api/*` is what the Flutter apps (Customer, POS, Delivery) talk to.

## Why MySQL changes one migration's approach

`product_prices` needs "at most one global price per product, and at most
one price per product per franchise" - the nullable `franchise_id` makes
that awkward on any database, since both MySQL and Postgres treat every
NULL as distinct for uniqueness purposes. Postgres has partial indexes to
work around it; MySQL doesn't, so that migration instead adds a generated
column (`COALESCE(franchise_id, 0)`) and puts the unique index on that -
same guarantee, portable SQL. Every other migration was already
database-agnostic Laravel schema-builder code, so nothing else changed.

## How the 4 login portals work

One `users` table for all 7 roles (Customer, Pharmacist, Franchise Staff,
Franchise Owner, Delivery Agent, Super Admin, Accountant). Two
*authentication mechanisms* sit on top of it, not four:

- **Token (Sanctum)** - `routes/api.php`, consumed by the Flutter apps.
  Portal separation is route-group prefix + role middleware
  (`/api/customer/*`, `/api/franchise/*`, `/api/delivery/*`, `/api/admin/*`).
- **Session (`web` guard)** - `routes/web.php`, the server-rendered
  Franchise/Admin portal you're looking at in the browser. Same login
  rules (password + 2FA for Super Admin), same role checks, just a
  cookie/session instead of a bearer token.

Franchise-scoped users (Owner/Staff/Pharmacist/Delivery Agent) carry a
`franchise_id`; Customer/Super Admin/Accountant do not.

## Multi-tenancy middleware: `franchise.scope`

`app/Http/Middleware/EnsureFranchiseAccess.php`, aliased in
`bootstrap/app.php`. Applied to the franchise/delivery route groups in
both `routes/api.php` and `routes/web.php`. It inspects every route-model-
bound parameter in the request; if any of them has a `franchise_id`
column, it's checked against the logged-in user's own `franchise_id` and
aborts with 403 on a mismatch. Super Admin / Accountant are exempt (they
see everything). This is the actual security boundary that stops a
Franchise Staff request from reading another store's orders/inventory by
editing an id in the URL - it works automatically on any future route that
type-hints a scoped model, no per-controller code needed.

## Web portal (Tailwind v4 + Vite + Alpine)

Real pages, not mockups - `/login`, `/two-factor`, and a dashboard shell
for each portal (`franchise.dashboard`, `admin.dashboard`), sharing one
authenticated app layout (`components/layouts/app.blade.php`) with a
role-aware sidebar. Dashboard numbers are clearly-labelled sample data
until Customer Orders / Purchase & Vendor / Settlement give them something
real to read from.

Design direction, if you want to extend it consistently: deep pharmacy-
teal + warm honey/amber (not the generic AI-cliché cream+terracotta),
Space Grotesk for headings/chrome, Inter for body/data, IBM Plex Mono
reserved *only* for batch numbers/SKUs/expiry dates. The one signature
element is the "freshness" system (`.freshness-ok/-approaching/-urgent` in
`resources/css/app.css`) - a green→amber→red indicator on any batch/
inventory reference, tied to days-to-expiry, since FEFO/expiry is the
actual idea this whole business runs on. Reuse those three classes rather
than inventing a new status-color scheme when you build out Inventory.

Tailwind v4's setup is CSS-first - there's no `tailwind.config.js`; every
custom color/font lives in the `@theme` block at the top of
`resources/css/app.css`. Add tokens there, not a config file.

## Deliberate additions beyond the SRS's explicit schema

- `order_items` — the SRS's Orders table had no line items; orders can't
  work without them.
- `addresses`, `categories` — implied by SRS 3.1 ("Profile & address
  management", "Category browsing") but not in the schema list.
- `delivery_assignments`, `franchise_settlements` — needed for flows #8 and
  #9 but not listed as tables.
- `product_prices` — separate from `products`, since SRS 3.6 calls for
  both "Global & franchise pricing."
- `franchises.drug_license_number`, `products.drug_schedule`,
  `products.hsn_code` — added as fields, not yet wired into any legal
  logic. Confirm the schedule values (OTC/H/H1/X) with your compliance
  side before relying on them.
- Audit logging uses `spatie/laravel-activitylog` rather than a hand-rolled
  table — `use LogsActivity;` is already on the models most likely to need
  a trail (PurchaseOrder, GoodsReceipt, SupplierInvoice/Payment, Order,
  CustomerPayment, Refund, Prescription, Franchise, Supplier).

## Auth module

**Customer login** - `POST /api/auth/customer/otp/request` `{mobile}`, then
`POST /api/auth/customer/otp/verify` `{mobile, code, name?}`. First
successful verification creates the account and assigns the `Customer`
role. Returns `{user, token}`.

**Staff/Admin login (API)** - `POST /api/auth/staff/login` `{login, password}`.
Super Admin (or anyone with `two_factor_enabled`) gets
`{two_factor_required: true, challenge}` instead of a token - send that
challenge + the OTP to `POST /api/auth/staff/two-factor/verify`
`{challenge, code}` for `{user, token}`.

**Staff/Admin login (web)** - `/login` and `/two-factor` do the same thing
through a browser session instead of a token.

**Testing locally without real SMS**: leave `SMS_PROVIDER=log` in `.env` -
OTP codes get written to `storage/logs/laravel.log` instead of sent as SMS.
Switch to `SMS_PROVIDER=msg91` when ready - see the DLT-registration note
in `Msg91SmsProvider` first, or OTPs get silently dropped by the carrier.

**Seeded Super Admin**: mobile `9999999999`, password `password`, 2FA on.
Remove/change before staging - see `DatabaseSeeder`.

**If role checks silently fail**: check `config/permission.php` for a guard
mismatch - Spatie needs to check roles against the `sanctum` guard
(matching `config/auth.php`'s default), not its own package default of
`web`.

## Customer Orders module

The full loop: browse → cart → checkout → pay → fulfill → track → refund.

**Browse** - `GET /api/customer/products?q=&category_id=&franchise_id=` (search
by name/salt/brand per SRS 3.1). Pass `franchise_id` to get real price +
stock for that store; omit it for a price-only, stock-blind browse.

**Cart** - `GET /cart`, `POST /cart/select-franchise`, `POST /cart/items`,
`PATCH /cart/items/{id}`, `DELETE /cart/items/{id}`. Server-persisted per
customer, matching the SRS's `POST /cart/add` endpoint.

**Prescriptions** - `POST /customer/prescriptions` (multipart file upload,
stored on a private disk, never a public URL) → pharmacist reviews via
`GET /franchise/prescriptions` + `POST /franchise/prescriptions/{id}/verify`
(role:Pharmacist specifically, nested inside the Franchise Portal group).

**Checkout** - `POST /customer/orders` `{franchise_id, fulfillment_type,
address_id?}`. Soft stock check + the prescription gate (an approved,
not-yet-linked prescription must exist if any cart item needs one) happen
here; nothing is reserved yet - see below for why.

**Payment** - `POST /customer/orders/{id}/payments/initiate` creates a
Razorpay order and returns what the client needs to open Checkout.
`POST /customer/payments/verify` is the fast path right after the client-
side handler fires; `POST /payments/webhook` (unauthenticated, Razorpay-
signed) is the real source of truth, since 3-5% of customers close the
browser before any client callback runs. Both funnel into the same
idempotent `PaymentConfirmationService` - safe to fire twice for one payment.

**Why reservation happens at payment success, not at checkout**: the SRS
flow is explicit - Payment SUCCESS → Order CONFIRMED → Stock RESERVED. So
`POST /orders` only soft-checks availability; the actual hold
(`inventory.reserved_quantity`) is placed once money has actually moved.
That leaves a real gap - stock can theoretically run out in the minutes
between checkout and payment completing - so `PaymentConfirmationService`
catches that case explicitly and logs it as critical rather than silently
losing the order; auto-refunding it is flagged below, not built yet.

**Fulfillment** - `POST /franchise/orders/{id}/status` walks
confirmed → preparing → ready_for_dispatch → out_for_delivery →
delivered/picked_up, with each transition checked against what it's
allowed to follow from. FEFO batch deduction and GST invoice generation
both fire exactly once, at the delivered/picked_up step - not before -
matching "Payment ≠ Revenue until fulfilled." A single order line can now
draw from more than one batch (`order_item_batches`), not just the nearest-
expiry one, since real stock is rarely all in one batch.

**Refunds** - `POST /customer/orders/{id}/refunds` `{order_item_ids, reason}`
covers cancelling before fulfillment (stock was only reserved, so it's
released, not un-deducted). Refunding an already-delivered order is a
return - needs physical stock to come back in - and isn't built yet;
that request is rejected with an explicit message rather than mishandled.

**Live updates (Reverb)** - `OrderStatusUpdated` broadcasts on a private
`order.{id}` channel (customer tracking); `NewOrderPlaced` broadcasts on
`franchise.{id}` (the dashboard's live order queue). Channel access rules
are in `routes/channels.php`. Both fire from `PaymentConfirmationService`
and `OrderFulfillmentController` - nothing extra to wire up per-route.

**Testing payments locally**: `PAYMENT_GATEWAY=log` in `.env` fabricates
gateway ids and accepts every signature (never use outside local/testing) -
same pattern as `SMS_PROVIDER=log`. Switch to `PAYMENT_GATEWAY=razorpay`
with real keys when ready.

## Deliberate simplifications in this pass

- Invoices are a data record (number, GST breakdown) - PDF rendering is a
  separate pass.
- Delivery pricing (distance/minimum-order fees) isn't modelled;
  `delivery_charge` stays 0.
- If stock runs out between checkout and payment success, the payment
  still succeeds (money moved) but reservation fails - logged as critical,
  not auto-refunded yet.
- Refunds/returns on already-fulfilled (delivered/picked-up) orders aren't
  supported - only pre-fulfillment cancellation is.
- Razorpay Route (split settlement) and RazorpayX Payouts - the pieces
  that matter for Franchise Settlement - aren't wired in; this pass only
  covers customer-to-platform payment, not platform-to-franchise payout.

## Purchase & Vendor module

PO → approval → GRN (inventory increases here, and only here) → supplier
invoice capture + matching → payment → vendor ledger.

**Suppliers** - onboarded centrally: `POST /admin/suppliers` (Admin only).
Franchises get read-only access via `GET /franchise/suppliers` to pick one
when raising a PO - suppliers aren't franchise-owned, since the same vendor
typically supplies more than one store.

**Purchase Orders** - `POST /franchise/purchase-orders`
`{supplier_id, expected_date?, items: [{product_id, ordered_qty,
expected_rate}]}`. Any Owner/Staff/Pharmacist can create one; approving it
(`POST .../approve`) or rejecting it is narrowed to `role:Franchise Owner`
specifically - the person who raised the order shouldn't be the only one
who can also approve it. Admin can see every franchise's POs
(`GET /admin/purchase-orders?franchise_id=&status=`) but approval stays a
franchise-level action.

**GRN** - `POST /franchise/purchase-orders/{id}/grn`
`{received_date, items: [{product_id, batch_no, expiry_date, received_qty,
damaged_qty?, purchase_rate, mrp?}]}`. This is the only thing that
increases `inventory.quantity`, matching the SRS rule verbatim. Only the
*undamaged* portion of a line becomes sellable stock - `damaged_qty` is
recorded on the GRN for the record but doesn't inflate what's available to
sell (there's no separate return/credit-note flow for it yet, flagged
below). Real deliveries often arrive in more than one shipment, so a PO
only flips to `completed` once every line is fully received, not after the
first GRN against it.

**Supplier invoices** - `POST /franchise/supplier-invoices`
`{supplier_id, purchase_order_id?, goods_receipt_id?, invoice_number,
invoice_date, invoice_amount, gst_amount, due_date?}`. If a GRN is linked,
`InvoiceMatchingService` compares the invoice's declared amount against
what that GRN's lines actually add up to (2% tolerance for rounding) and
sets status to `matched` or `disputed` automatically - "PO-GRN-Invoice
matched" from the SRS flow. Only a `matched` invoice can be approved
(`role:Franchise Owner`); a `disputed` one needs a human to sort out first.

**Payments** - `POST /franchise/supplier-invoices/{id}/payments`
`{amount, payment_date, payment_mode, reference_number?}`
(`role:Franchise Owner`). Validates the payment doesn't exceed what's
actually left on the invoice, and flips the invoice to `paid` once fully
settled - supports partial payments along the way.

**Vendor ledger** - `GET /franchise/vendors/outstanding` and
`GET /franchise/vendors/{supplier}/ledger` (chronological invoice-debit /
payment-credit statement with a running balance), both scoped to the
calling franchise's own transactions with that supplier even though the
supplier itself is shared network-wide. `GET /admin/vendors/outstanding`
and its ledger equivalent see everything, unscoped - this is genuinely
computed on the fly from invoices/payments each call, not a separately
maintained ledger table that could drift out of sync.

## Deliberate simplifications in this pass

- Invoice matching compares the *total* against the GRN - not a per-line
  check (a rate discrepancy on one product inside an otherwise-correct
  total wouldn't be caught).
- Damaged/leaked stock from a GRN is recorded but there's no credit-note-
  back-to-supplier or replacement-shipment flow yet - the SRS itself flags
  this as optional ("may be incorporated if needed").
- Vendor payments are recorded as already-completed (bank transfer/UPI/
  cheque reference typed in after the fact) - there's no live payment-
  gateway/banking-API integration for actually *sending* the money, unlike
  the customer-side Razorpay integration.

## POS module

Real-time, final sale - no reservation phase, unlike Customer Orders.

**Product lookup** - `GET /franchise/pos/products?barcode=` or `?q=`. A
barcode scanner is just a fast keyboard - `?barcode=` is what a focused
input field wired to the scanner would submit, no special hardware
integration needed on this end.

**Ringing up a sale** - `POST /franchise/pos/sales`
`{items: [{product_id, quantity}], payment_mode: cash|upi|card,
customer_id?, walk_in_name?, walk_in_phone?, prescription_note?}`.
`customer_id` is optional - most POS sales are anonymous walk-ins, which
is why `orders.user_id` had to become nullable (see the migration for why).
Stock check, FEFO deduction, payment, and invoice generation all happen in
one transaction - matches "POS sales are real-time final sales" from the
SRS exactly. POS orders are created directly in the same terminal status
online orders reach at delivery (`picked_up` reused rather than adding a
dedicated POS status - see the comment on `Order::isRevenueRecognised()`).

**Prescription items at the counter** - handled differently from the
online flow on purpose. Online: upload → separate async pharmacist review.
POS: the person ringing up the sale is standing in front of the physical
prescription right now, so instead of a review step, only a
`role:Pharmacist` can process that specific sale, and they leave a
`prescription_note` (doctor/registration reference) for the audit trail -
checked and persisted, not just validated and discarded.

**Reports** - `GET /franchise/pos/sales?date=` (today's sales, or any
date) and `GET /franchise/pos/summary?date=` (count, total, breakdown by
payment mode) - the "Daily sales & cash report" from SRS 3.3.

**Live dashboard ticker** - POS sales fire the same `NewOrderPlaced` event
online orders do, on the same `franchise.{id}` channel - a Franchise
Owner watching the dashboard sees POS sales land in real time alongside
online orders, no separate wiring needed.

## Deliberate simplifications in this pass

- Payment is recorded as already-successful the moment the sale completes
  - no card-machine/UPI-QR integration; staff select the mode after the
  customer has actually paid, same as typing a reference number in after
  the fact.
- No offline/poor-connectivity mode - every sale is a live API call. Real
  POS hardware often needs to keep selling through a dropped connection;
  that's a genuinely different architecture (local-first with sync) and
  out of scope for this pass.
- No receipt printing integration (thermal printer output) - the invoice
  record exists, but nothing formats/sends it to a printer yet.

## Delivery module

`Order marked ready_for_dispatch → assigned → picked up → out for delivery
→ delivered → proof of delivery` - the SRS flow, end to end.

**Assignment** (Franchise) - `POST /franchise/orders/{id}/assign-delivery`
`{delivery_agent_id}`. Only valid from `ready_for_dispatch`, only for
`fulfillment_type: delivery` orders (pickup orders don't need an agent),
and the given agent has to actually hold the Delivery Agent role *and*
belong to the same franchise as the order - checked in the request, not
assumed from a plausible-looking id.

**Agent actions** - `GET /delivery/assignments` (own active assignments),
`POST .../picked-up`, `POST .../delivered` (multipart, requires a
`proof_of_delivery` file - photo or signature capture, stored on a private
disk like prescriptions), `POST .../failed` `{failure_reason}`. Marking
delivered on a failed attempt is deliberately left to a franchise-side
follow-up (re-assign, retry) rather than this endpoint deciding that on
the agent's behalf.

**Refactor worth knowing about**: picked-up and delivered don't just
update the assignment - they drive the *order's* status too
(`out_for_delivery`, `delivered`), which is what actually triggers FEFO
deduction and invoice generation. That logic used to live only inside
`Api\Franchise\OrderFulfillmentController`; it's now
`OrderFulfillmentService`, called by both that controller and the new
Delivery one, so there's exactly one place status transitions and their
side effects live, not two copies that could drift apart.

**A genuine gap worth naming**: `DeliveryAssignment` has no `franchise_id`
column, so `franchise.scope` middleware (already applied to `/delivery/*`
for consistency) can't actually enforce anything there - an agent's real
boundary is "my own assignments," not "my franchise's." Every action in
`Api\Delivery\AssignmentController` checks `delivery_agent_id` against the
caller explicitly; that check, not the middleware, is what's actually
protecting these routes. Said plainly in the code and here rather than
left for someone to discover the hard way.

## Franchise Settlement module

`Orders fulfilled by franchise → System calculates franchise share/
commission → Settlement statement generated → Payment released → marked
completed` - the SRS flow, end to end. Admin-only to generate or release;
a franchise can see its own settlement history but can't generate or
approve its own payout (same separation-of-duties reasoning as Purchase &
Vendor approvals).

**Only online orders count.** POS revenue is collected directly by the
franchise at the counter - it's never in the central account, so there's
nothing to settle for it. `SettlementService::generate()` explicitly
excludes `fulfillment_type = pos` when calculating gross sales; only
delivery/pickup orders that reached `delivered`/`picked_up` within the
period are included, scoped by `delivered_at` (when revenue was actually
recognised) rather than when the order was placed.

**Generate** - `POST /admin/franchises/{id}/settlements/generate`
`{period_start, period_end}`, or `POST /admin/settlements/generate-all`
for every active franchise in one call (one franchise's overlap or
zero-sales period doesn't block the rest of the batch). Refuses to
generate a settlement whose period overlaps an existing one for that
franchise - the one safeguard against double-paying that felt worth
building now rather than leaving as a "be careful" note.

**The commission direction is worth double-checking if you touch this
code**: `franchises.commission_percentage` is what HQ *keeps* - the
franchise's `net_payable` is the remainder, `gross_sales - commission_amount`.
That's confirmed against the comment already on that column in its
migration, not assumed fresh here - getting this backward would mean
over- or under-paying a real franchise, so it's flagged deliberately
loudly in `SettlementService`.

**Release** - `POST /admin/settlements/{id}/release`. Uses RazorpayX's
Composite Payout API (creates a Contact, Fund Account, and Payout in one
call - a plain payout actually needs all three, which their own docs
flag and recommend the composite endpoint specifically to avoid). Needs
the franchise's bank details on file first -
`PATCH /admin/franchises/{id}/bank-details`. `PAYOUT_GATEWAY=log` (the
default) fabricates a payout id and logs instead of sending real money,
same pattern as `SMS_PROVIDER`/`PAYMENT_GATEWAY` - kept as a *separate*
env var from `PAYMENT_GATEWAY` on purpose, so customer payments can go
live before you're ready to send real payouts.

**Flagged directly in `RazorpayXPayoutGateway`, worth repeating here**:
verify the field names against your own RazorpayX dashboard before going
live. Two specifics that are easy to get wrong: `account_number` in the
payout request is *your own* RazorpayX business account, not the
franchise's (confusingly named - Razorpay's docs call out passing the
recipient's account there as a common mistake); and `purpose` has to be a
classification already configured on your dashboard, since it can't be
created via the API.

## Admin Panel

The core Admin Control flow from the SRS: configure products/prices/users/
franchises, system enforces rules automatically (RBAC + validation, not a
separate rules engine), admin monitors from here.

**Staff onboarding** - `POST /admin/users` `{name, mobile, email?, password,
role, franchise_id?, two_factor_enabled?}`. This is worth calling out
plainly: **it's the only way any account except Customer ever comes into
existence.** Franchise Owner, Franchise Staff, Pharmacist, Delivery Agent,
Super Admin, Accountant - all created here, all password-based from day
one (no invite-link/self-set-password flow yet - the admin sets an initial
password directly and passes it along through a secure channel outside the
system). Two guards worth knowing: `franchise_id` is required for the four
franchise-scoped roles and rejected for the two central ones (keeps the
"null franchise_id = central" convention actually true everywhere else
that relies on it - `franchise.scope` middleware, settlement scoping);
and only an existing Super Admin can create another Super Admin or a
Accountant - Accountant can create everyone else, but not another
central account. `POST /admin/users/{id}/reset-password` is the only
account-recovery path that exists - there's no self-service "forgot
password" for staff.

**Franchise onboarding** - `POST /admin/franchises` (name, address,
commission_percentage, etc. - slug auto-generated and de-duplicated, same
pattern as products), `PATCH /admin/franchises/{id}` for everything except
bank details, which stay on their own endpoint from the Settlement pass.

**Catalog & pricing** - `POST /admin/categories`, `POST /admin/products`
(name, salt composition, HSN code, `drug_schedule`, prescription
requirement), `POST /admin/products/{id}/prices` `{franchise_id?, mrp,
selling_price, tax_percentage}` - omit `franchise_id` for the global
default price, include it to override for one store. This is an upsert
(`updateOrCreate` on the product+franchise pair) - calling it again for
the same franchise updates that price rather than creating a duplicate,
which the unique index from the Customer Orders pass would reject anyway.

**Audit log** - `GET /admin/audit-log?subject_type=Order&causer_id=&from=&to=`.
Not new data - every model with `LogsActivity` (Order, PurchaseOrder,
CustomerPayment, Refund, Prescription, and others going back to the schema
pass) has been writing here the whole time via `spatie/laravel-activitylog`;
this is the first endpoint that actually reads it back. `subject_type`
takes a short name (`Order`, not the full `App\Models\Order` string) for a
slightly friendlier query param.

## Reporting & Accounting module

`Daily transactions → Sales register → Purchase register → GST reports →
Vendor outstanding report → Franchise settlement report → Export to
Tally/ERP` - the last flow from your System Flow doc. The last two steps
already existed (`Api\Franchise\VendorLedgerController`,
`Api\Admin\SettlementController`, from the Purchase & Vendor and
Settlement passes) - this fills in the rest.

**Sales Register** - `GET /admin/reports/sales-register?from=&to=&
franchise_id=&format=csv`. Every revenue-recognised sale in the period -
online *and* POS both. Worth contrasting with Franchise Settlement's
`gross_sales`, which deliberately *excludes* POS (nothing to pay a
franchise back for money it already collected at its own counter). A
sales register is a record of what was sold, not a statement of what's
owed to whom - both channels belong in it, for different reasons than
Settlement's numbers.

**Purchase Register** - `GET /admin/reports/purchase-register?...`. Built
from supplier invoices, not GRNs - a GRN has no GST figure, and a purchase
register feeding into a GST report needs one.

**GST summary** - `GET /admin/reports/gst-summary?from=&to=&franchise_id=`.
Output GST (from the sales register) minus input GST (from the purchase
register) = net payable. Summary-level only - an actual GSTR-1 filing
needs HSN-code-wise and tax-rate-wise breakdowns, which both registers
have the underlying data for but this doesn't group by yet.

**Payment/order reconciliation** - `GET /admin/reports/payment-mismatches`
(SRS 3.5's "Order vs payment mismatch report"). No date range - this is
current-state data-integrity checking, not a historical report. Flags two
directions: an order that moved past `pending_payment` with no successful
payment behind it, and the reverse - a successful payment sitting on an
order that's still `pending_payment`, which almost always means a webhook
silently failed and the client-side verify call never landed either.
`PaymentConfirmationService` is written to make the first case impossible
in normal operation, but a reconciliation report exists precisely so
"shouldn't happen" isn't the only thing standing behind that.

**Export** - `?format=csv` on the sales/purchase registers streams a CSV
rather than JSON, for Tally/ERP import. A proper Tally XML integration
(their actual native import format) is a deeper, product-specific
integration than a generic CSV - most accounting tools including Tally
can import CSV directly, so that's the level this pass targets.

## Web portal - now fully built out

Every sidebar item in both portals is real now - no more "Soon" tags
except none at all. `GeneratesUniqueSlugs` (shared trait) and
`<x-form.field>` / `<x-form.select>` / `<x-form.textarea>` /
`<x-form.checkbox>` (reusable Blade components) came out of this pass and
are what make every form below consistent rather than independently
reinvented.

**Admin**: Franchises (full CRUD + bank details), Products & Pricing
(categories, products, global/franchise-specific pricing), Vendors
(suppliers, network-wide outstanding, per-supplier ledger), Settlements
(generate one or all, release), Reports (all 4 types + CSV export), Audit
Log (filterable).

**Franchise**: Orders (status transitions only ever offer what would
actually succeed - see `OrderFulfillmentService::validNextStatuses()` -
plus delivery assignment), POS (the one genuinely interactive page -
Alpine-driven cart, submitted as a plain form post via dynamically
generated hidden inputs, not an AJAX/SPA layer), Inventory (the
freshness-indicator design language applied to real batch data for the
first time), Prescriptions (approve/reject, private-disk image serving -
never a public URL), Purchase Orders (the full lifecycle - create,
approve/reject, GRN, invoice capture, payment - as one page with
conditional sections per current state), Settlements (read-only, added
alongside the other 10 since leaving it as the sole remaining gap didn't
make sense).

Every web controller is deliberately separate from its API sibling -
same underlying services, same Form Request validation classes wherever
the shape matched, but redirects+Blade and JSON are different enough
response shapes that forcing one controller to do both wasn't worth it.

One bug worth knowing about if you build more forms on this pattern:
`old()` and `$errors` need dot notation for nested arrays
(`items.0.batch_no`), but an `<input>`'s `name=` attribute needs bracket
notation (`items[0][batch_no]`) to actually submit as an array. The form
components convert automatically now - discovered and fixed while
building the GRN form's per-line-item inputs.

## Not included yet (next steps)

- HSN-code-wise / rate-wise GST breakdowns (GSTR-1-ready detail, not just
  the summary total).
- Live Razorpay settlement reconciliation - comparing what Razorpay
  actually deposited against what `customer_payments` shows. The
  `settlement_date` column has existed since the schema pass but nothing
  populates it yet; that needs Razorpay's Settlements API, not just data
  already sitting in this system.
- Native Tally XML export, if CSV import isn't enough for your workflow.
- Discount/coupon management.
- A proper staff invite flow (email/SMS link + self-set password) instead
  of an admin-typed initial password.
- A "draft" review step before a settlement is finalized - `generate()`
  goes straight to `generated`; the `draft` status exists in the schema
  but isn't used yet.
- Automatic re-assignment or retry logic after a failed delivery attempt.
- Fine-grained permissions beyond the 7 roles - a Franchise Staff and a
  Franchise Owner can still do exactly the same things everywhere except
  the handful of routes explicitly narrowed to `role:Franchise Owner`.
- Octane: the app is written to avoid the usual state-leak patterns, so
  you can add it whenever you want with `composer require laravel/octane`
  then `php artisan octane:install --server=frankenphp`.

Every flow from the System Flow document now has real, working code
behind it. What's left above is depth within each one, not a missing
module - tell me which of these (or anything else) is worth tackling next.
