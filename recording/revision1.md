# Revision 1 — Test Guide

Step-by-step test plan para sa mga inupdate base sa `revision1.txt`.
Login bilang **dean** (`dean / dean`) o **program chair** unless stated.

---

## 0. Prerequisites

1. Make sure XAMPP MySQL is running.
2. Run the migration kung hindi pa:

   ```
   "C:/xampp/mysql/bin/mysql.exe" -u root cursescheduling < db/migration_revision1.sql
   ```

3. Verify migration:

   ```sql
   SELECT COUNT(*) FROM rooms;                     -- expect 5
   SELECT course_tier, COUNT(*) FROM curriculum GROUP BY course_tier;
   -- expect: gen_ed ~899, gen_eng ~567, major ~2001
   SELECT pairing, COUNT(*) FROM curriculum GROUP BY pairing;
   -- expect: NONE ~2543, TTH ~924
   ```

---

## 1. Adding new room numbers

> Sagot sa: "Pano po nakapag add ng ibang room number don?"

**Path:** `dean/rooms.php`  →  *Manage Rooms* card

### Test A — Add room

1. Click **Add Room**.
2. Fill: Room Number = `BSME-LAB-1`, Type = `Laboratory`, Capacity = `30`.
3. Click **Save**.
4. ✅ Bagong row lumabas sa Manage Rooms table na may `Active` chip.
5. ✅ Pumunta sa `dean/fac_sched.php` → Auto-Generate → "Available Rooms".
   - `BSME-LAB-1` should appear sa quick-add row at naka-chip na agad.

### Test B — Edit room

1. Click **Edit** sa existing row.
2. Baguhin Capacity → `45`. Click **Save**.
3. ✅ Updated capacity reflects sa table.

### Test C — Disable room

1. Click **Disable** sa isang room.
2. Reload `fac_sched.php` Auto-Generate modal.
3. ✅ Disabled room **hindi** dapat mag-appear sa quick-add (loaded with `active=1` filter).

### Test D — Duplicate guard

1. Click Add Room. Type `301` (existing).
2. ✅ Alert: `"Room already exists."`

### Test E — Delete room

1. Click **Delete** → confirm.
2. ✅ Row na-tanggal sa Manage Rooms.

---

## 2. Faculty: name, available time, courses they teach

> Sagot sa: "Faculty name and ung available time and tinuturo na courses"

**Path:** `dean/faculty.php`

### Test A — Profile modal

1. Sa Faculty Directory, click sa profile / availability button ng isang faculty (e.g. `juanzz dela cruz`).
2. ✅ Modal ipakita: Faculty name (header), Weekly Availability grid (Mon–Sat with from/to), Specializations subject-search.

### Test B — Save availability + specializations

1. Add availability:
   - Monday `08:00 – 12:00`
   - Wednesday `13:00 – 17:00`
   - Friday `08:00 – 12:00`
2. Search specializations → add `CPE 1102L`, `CPE 324`, `MAT.402`.
3. Click **Save Profile**.
4. ✅ Toast: `"Faculty profile saved."`

### Test C — Verify persisted

```sql
SELECT user_id, availability, specializations FROM faculty_meta WHERE user_id = 2;
```

✅ Both columns should contain valid JSON matching what you entered.

---

## 3. Curriculum-year + faculty selector sa auto-generate

> Sagot sa: "kulang lang po nung pipili ng faculty at curriculum year — Since iba iba po curriculum namin"

**Path:** `dean/fac_sched.php`  →  **Auto-Generate Schedule**

### Test A — Curriculum year list

1. Open the modal.
2. Pick Program = `BSCoE`.
3. ✅ Dropdown **Curriculum Year** populates with multiple years (e.g. `2022-2023`, `2018-2019`, `2013-2014`, …) — proves multi-curriculum support.
4. Switch program → `BSME`. ✅ List should refresh to BSME's curriculum years (`2022-2023` etc.).

### Test B — Filter actually applies

```bash
curl "http://localhost/CourseScheduling_updated/cs_modified/controller/end-points/get_controller.php?requestType=get_subjects_by_program_year&program=BSCoE&year_level=1&semester=1st&curriculum_year=2018-2019"
```

✅ Returned rows all have `"curriculum_year":"2018-2019"`.

---

## 4. Course-tier prioritization (Gen Ed → Gen Eng → Major)

> Sagot sa: "mauuna ng mga courses kagaya ng Gen Ed courses, kagaya ng FCL… General Engineering sina Doc Jun… tapos kami naman."

**Path:** `dean/fac_sched.php`  →  **Auto-Generate Schedule**

### Test A — Tier picker

1. Open the modal. ✅ Tatlong pill cards: **Gen Ed**, **General Engineering**, **Major / Professional** (default checked = Major).

### Test B — Plotting order workflow

Follow ang exact order from revision1.txt:

#### Step 1 — GEC user manually plots Gen Ed

- Login as `gec / gec`.
- Open **Schedule → Create Schedule**.
- Plot a Gen Ed subject (e.g. `FCL 100`, `SOC300`, `ENG 002`) sa MWF 10:00–11:00.
- Save.

#### Step 2 — Engineering plots General Engineering

- Login back as dean.
- Open Auto-Generate. Pick:
  - Program = `BSCoE`, Year = `1`, Semester = `1st`
  - Tier = **General Engineering**
  - Rooms: `301,302,303`
- Click Generate.
- ✅ Generated entries should **not** overlap any Gen Ed slot the GEC user just saved.

#### Step 3 — Auto-plot Major

- Same modal, change Tier = **Major / Professional**. Generate.
- ✅ New entries avoid both Gen Ed and Gen Eng slots na naka-plot na.

### Test C — Confirm via DB

```sql
SELECT subject_code, course_tier FROM curriculum
WHERE subject_code IN ('FCL 100','CHEM 103','CPE 1102L','MAT.402');
```

✅ Expected:
- `FCL 100` → `gen_ed`
- `CHEM 103` / `MAT.402` → `gen_eng`
- `CPE 1102L` → `major`

---

## 5. Day-pairing preference (TTH / MWF / WS)

> Sagot sa: "Programming Logic and Design… ang pairing ay TTH. So dapat naka-plot siya doon lang sa TTH. Hindi siya pwedeng i-plot sa ibang… hangga't maaari na pwede."

### Test A — Lab courses tagged TTH

```sql
SELECT subject_code, lab_hours, pairing FROM curriculum
WHERE subject_code='CPE 1102L';
```

✅ `pairing = TTH`.

### Test B — Auto-gen respects pairing

Pre-condition: faculty (e.g. `juanzz`) has availability on Tuesday and Thursday.

1. Auto-Generate `BSCoE` Year `1`, `1st` semester, Tier = Major.
2. Inspect the saved schedule.
3. ✅ `CPE 1102L` (Programming Logic and Design) sessions land on **Tuesday and Thursday**, hindi sa Mon/Wed/Fri/Sat.

### Test C — Fallback to other days

- Block Tuesday + Thursday in faculty availability (set both as empty).
- Auto-Generate ulit.
- ✅ Lab still gets plotted (kahit sa MWF/WS) instead of being unassigned — proves graceful fallback per the spec: *"Pero kun maikita naman na hindi na nga siya possible, pwede namang i-plot na sa iba."*

---

## 6. End-to-end smoke test (5-min pass)

1. **Login as dean** → `rooms.php`.
2. Add room `BSCoE-301`. Verify chip appears in Auto-Generate modal.
3. **Login as faculty** (`juanzz / juanzz`) — set availability + specializations.
   *(Or do this via dean's Faculty Profile modal.)*
4. Logout → **Login as gec** → Plot 1 Gen Ed manually.
5. Logout → **Login as dean** → `fac_sched.php`.
6. Auto-Generate **General Engineering** for BSCoE Y1 1st sem.
7. Auto-Generate **Major** for the same cohort.
8. ✅ Open `view_fac_sched.php?sch_id=…` and confirm:
   - No time overlaps within a faculty.
   - No Gen Ed slot was overwritten.
   - Lab/paired courses sit on TTH (or MWF if forced fallback).
   - All `room` fields are from your active rooms list.

---

## 7. Quick API sanity tests

```bash
# rooms
curl "http://localhost/CourseScheduling_updated/cs_modified/controller/end-points/get_controller.php?requestType=get_rooms&active=1"

# curriculum years per program
curl "http://localhost/CourseScheduling_updated/cs_modified/controller/end-points/get_controller.php?requestType=get_curriculum_years&program=BSCoE"

# tier-filtered subjects
curl "http://localhost/CourseScheduling_updated/cs_modified/controller/end-points/get_controller.php?requestType=get_subjects_by_program_year&program=BSCoE&year_level=1&semester=1st&tier=gen_eng&curriculum_year=2018-2019"

# auto-generate (Gen Ed tier, BSCoE Y1 1st sem)
curl -X POST "http://localhost/CourseScheduling_updated/cs_modified/controller/end-points/post_controller.php" \
  -d "requestType=auto_generate_schedule&program=BSCoE&year_level=1&semester=1st&tier=gen_ed&curriculum_year=2018-2019&rooms=301,302,303"
```

---

## Rollback (kung kailanganin)

```sql
-- remove only the new pieces
ALTER TABLE curriculum DROP COLUMN pairing;
ALTER TABLE curriculum DROP COLUMN course_tier;
DROP TABLE rooms;
```
