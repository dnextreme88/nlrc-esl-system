# Zeldan NLRC's ESL System

An English as a Second Language (ESL) system built for Zeldan Nordic Languages Review Center using the TALL stack (Tailwind, Alpine, Laravel, and Livewire).

## TODOS NOTES (draft)
- According to Ms. Jan Claire, textbooks / courses -> has lessons. this one is important and probably a must have in the system
- Certifications are issued to students who completed the ESL course. Certificate printing with PDF (may have to use Kendo UI package?)
- ~~Duration of booking of schedules: 30 minutes (25 minutes can be strategized by the teacher)~~
- Feedback/Meeting remarks (new table?) - a system that lets teachers make comments to the student after a meeting has been conducted (more like a survey that gives them feedback)
- ~~Have 1-on-1 meetings between student and teacher~~ This is now multiple, up to 5 students per slot.
- Head/Licensed teachers are different from teachers because the latter could be part time employees of NLRC.
- If a student fails the final level of progression (Advanced), he must repeat the previous one (Intermediate). This won't apply if student fails Intermediate - he will not take Beginner but will just repeat Intermediate
- In registration, we can put a schedule for the students that best fits them. After registration, they can change this.
- May implement trial classes (not affordable?)
- Project name to be decided
- Purpose of stand-by teachers, if the student doesn't adhere to their "desired" schedule, they can join a meeting with a random teacher whose available at that time.
- Registrations are student accounts while instructor accounts are created in admin panel (use Filament package)
- ~~Schedule between teacher and student.~~ Implemented.
- Students can pick a slot from available teachers but based on whose teachers can teach a specific level of progression (eg. if a student passes up to Intermediate level, the list of teachers that should show up will be teachers who can teach up to Advanced level)
- Students have to pay before using the app (might use Laravel Spark / Laravel Cashier?). Payment can be made for: 1 month. If student has no money, they cannot book a schedule.
- Hierarchy of modules (unsure):
  * modules
    * units
        * courses
            * lessons

### Course Structure Overview (Levels?)
- From top to bottom
  * Beginner (A1)
  * Elementary (A2)
  * Intermediate (B1)
  * Upper intermediate (B2)
  * Advanced (C1 - C2)

### Meetings / Bookings
- Feature suggestion: Add the number of students already booked on the slot
- Maximum number of students per meeting slot: 5. Close the slot once its full
- Rescheduling is allowed up to 2 days after current date (according to Sir Nestor)

### Mock Assessments
- Head/Licensed teachers are the only ones who can conduct mock assessments. They must pass a certain point (TBD). Teacher conducting this should be randomized instead of the teacher who initially taught the student (to avoid bias results). Mock test and review sessions are conducted after doing all activities.
- Mock assessments can be conducted via a link for now (according to Ms. Jan Claire)
- Proposed estimated completion time: 4-6 weeks (self-paced!)
- Resources provided: video lessons, audio practice files, quizzes, writing tasks, live teacher booking option (if possible only - if student wants a teacher booked)

### Comparison from NLRC-Lumi project

NLRC-Lumi is an old project that focuses on training students (called trainees) before their deployment to Finland. They must pass the courses that would help enhance their skills in the Finnish language. Though the app was finished, it was never used by real end-users due to unforeseen circumstances.

- Announcements will still be present
- Meetings are still created by instructors
- No more batches/groups
- No more uploading of documents for user verification
- Remove "employment" infos from registration. We only need basic information from the students

### From Ms. Jan Claire's former ESL employment
- If students picked a slot, it should show up on the teacher's calendar as well as on the student's. They may cancel but this incurs a penalty
- Maximum allowable opening/closing of slots for teacher availability up to 1 month only (eg. if today is 2/4/25, teachers can open/close slots until 3/4/25 because maximum payment can be paid up to 1 month only)
- Reservation of schedules that will be opened/closed by ESL instructors and can be selected by students.
- Teachers can open/closed slots based on their availability with an interval of 30 minutes per day (for the next 7 days)
- Their payment methods are: Credit card info/bank account
