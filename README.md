# Zeldan NLRC's ESL System

An English as a Second Language (ESL) system built for Zeldan Nordic Languages Review Center using the TALL stack (Tailwind, Alpine, Laravel, and Livewire).

## Useful commands

With the release of Laravel 12, item #s 1 - 3 are now run with a single command `composer run dev` by default.

1. Run server
``php artisan serve``

2. Run Vite
``npm run dev``

3. Listen to Laravel events
``php artisan queue:listen``

4. Start Laravel Reverb WebSocket server
``php artisan reverb:start``

5. Clear cache and optimize files
``php artisan optimize:clear``

### Some personal suggestions / personal todos not covered in other sections
- [ ] Add notification event to notify teachers that their `meeting_slot` was selected and booked by a student. Also, add a way for the teachers to set a meeting_link (GMeet, Zoom etc.). We can also send an email to the teacher's email to notify them about this.
- [ ] Comments system on a unit detail page? Students may be able to ask for help and teachers may respond etc.
- [ ] Ratings system on a unit detail page? Just to make the app a little more reactive and lively. We could also show these to the user dashboard whatever units the student has already rated etc.

## TODOS NOTES (draft)
- [ ] According to Ms. Jan Claire, textbooks / courses -> has lessons. this one is important and probably a must have in the system
- [ ] Add ability to "register" teacher accounts in Admin Panel
- [ ] Certifications are issued to students who completed the ESL course. Certificate printing with PDF (may have to use Kendo UI package?)
- [x] Duration of booking of schedules: 30 minutes (25 minutes can be strategized by the teacher)
- [ ] Feedback/Meeting remarks (new table?) - a system that lets teachers make comments to the student after a meeting has been conducted (more like a survey that gives them feedback)
- [ ] Head/Licensed teachers are different from teachers because the latter could be part time employees of NLRC.
- [ ] If a student fails the final level of progression (Advanced), he must repeat the previous one (Intermediate). This won't apply if student fails Intermediate - he will not take Beginner but will just repeat Intermediate
- [ ] May implement trial classes (not affordable?)
- [ ] Project name to be decided
- [ ] Purpose of stand-by teachers, if the student doesn't adhere to their "desired" schedule, they can join a meeting with a random teacher whose available at that time.
- [x] Schedule between teacher and student
- [ ] Students have to pay before using the app (might use Laravel Spark / Laravel Cashier?). Payment can be made for: 1 month. If student has no money, they cannot book a schedule.
- [ ] Hierarchy of modules (unsure):
  * modules
    * units
      * lessons

### Meetings / Bookings
- [x] Feature suggestion: Add the number of students already booked on the slot
- [ ] Maximum number of students per meeting slot: 5. Close the slot once its full
- [ ] Rescheduling is allowed up to 2 days after current date (according to Sir Nestor)

### Mock Assessments
- [ ] Head/Licensed teachers are the only ones who can conduct mock assessments. They must pass a certain point (TBD). Teacher conducting this should be randomized instead of the teacher who initially taught the student (to avoid bias results). Mock test and review sessions are conducted after doing all activities.
- [ ] Mock assessments can be conducted via a link for now (according to Ms. Jan Claire)
- [ ] Proposed estimated completion time: 4-6 weeks (self-paced!)
- [ ] Resources provided: video lessons, audio practice files, quizzes, writing tasks, live teacher booking option (if possible only, if student wants a teacher booked)

### Comparison from NLRC-Lumi project

NLRC-Lumi is an old project that focuses on training students (called trainees) before their deployment to Finland. They must pass the courses that would help enhance their skills in the Finnish language. Though the app was finished, it was never used by real end-users due to unforeseen circumstances.

- [x] Announcements will still be present
- [x] Meetings are still created by instructors
- [x] No more batches/groups
- [x] No more uploading of documents for user verification
- [x] Remove "employment" infos from registration. We only need basic information from the students

### From Ms. Jan Claire's former ESL employment
- [ ] ~~If students picked a slot, it should show up on the teacher's calendar as well as on the student's~~. They may cancel but this incurs a penalty
- [x] Maximum allowable opening/closing of slots for teacher availability up to 1 month only (eg. if today is 2/4/25, teachers can open/close slots until 3/4/25 because maximum payment can be paid up to 1 month only)
- [x] Reservation of schedules that will be opened/closed by ESL instructors and can be selected by students.
- [x] ~~Teachers can open/close slots based on their availability with an interval of 30 minutes per day (for the next 7 days)~~ for the next 28 days
- [ ] Their payment methods are: Credit card info/bank account. May have to use Paymongo instead of Laravel Cashier/Stripe
