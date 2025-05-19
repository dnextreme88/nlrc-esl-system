# Zeldan NLRC's ESL System

An English as a Second Language (ESL) system built for Zeldan Nordic Languages Review Center using the TALL stack (Tailwind, Alpine, Laravel, and Livewire).

**Planned release**: sometime in May 2025

## Useful commands

1. Run server, Vite and listen to Laravel events and queues. This runs the ff. commands concurrently: ``php artisan serve``, ``npm run dev``, and ``php artisan queue:listen`` on a single command

```bash
composer run dev
```

2. Start Laravel Reverb WebSocket server. Add a ``--debug`` option at the end to track events
```bash
php artisan reverb:start
```

3. Build assets for production

```bash
npm run build
```

4. Clear cache and optimize files

```bash
php artisan optimize:clear
```

5. Fix code styles based on config from [pint.json](https://github.com/dnextreme88/nlrc-esl-system/blob/main/pint.json)

```bash
composer run pint
```

6. Preview code styles that would be fixed when running composer run pint

```bash
composer run pint-test
```

## Some personal suggestions / personal todos not covered in other sections
- [ ] Charts in user dashboards for teachers/students to provide progress
- [ ] Comments system on a unit detail page? Students may be able to ask for help and teachers may respond etc.
- [ ] FAQ / Knowledge base for admin panel
- [ ] Ratings system on a unit detail page? Just to make the app a little more reactive and lively. We could also show these to the user dashboard whatever units the student has already rated etc.

---

## TODOS NOTES (draft)
- [ ] Certifications are issued to students who completed the ESL course. Certificate printing with PDF (may have to use Kendo UI package?)
- [ ] Head/Licensed teachers are different from teachers because the latter could be part time employees of NLRC.
- [ ] If a student fails the final level of progression (Advanced), he must repeat the previous one (Intermediate). This won't apply if student fails Intermediate - he will not take Beginner but will just repeat Intermediate
- [ ] May implement trial classes (not affordable?)
- [ ] Project name to be decided
- [ ] Purpose of stand-by teachers, if the student doesn't adhere to their "desired" schedule, they can join a meeting with a random teacher whose available at that time.
- [ ] Students have to pay before using the app (will use Paymongo for now). Eventually, we may have to use something else other than Paymongo to support other countries (as per meeting). Payment can be made for: 1 month. If student has no money, they cannot book a schedule.
- [ ] Hierarchy of modules (unsure):
  * modules
    * lessons (below is a sample order of content inside a lesson)
      * introduction
      * lesson objectives
      * warm-up activities
      * vocabulary and key terms
      * exercises
      * assessment task

### Meetings / Bookings
- [ ] Add meetings in admin panel. When viewing (not editing) a meeting, show the teacher who assigned that slot and students who reserved that slot. For now, make it read only since the meeting links are set on the teacher dashboard
- [ ] Feedback/Meeting remarks (new table?) - a system that lets teachers make comments to the student after a meeting has been conducted (more like a survey that gives them feedback)
- [ ] Improved meetings tracker. Currently implemented in a "hacky" way, add a new table in the DB to track meeting updates. Examples of stuff to track:
  * Teacher initially reserved the slot based on his/her availability
  * Student/s reserved the slot. Teacher will be notified
  * Once notified, teachers may set the meeting link on their meeting details page. Notify the students
  * Cancellations by teacher or his/her students
  * Reschedules by teacher
- [ ] Rescheduling is allowed up to 2 days after current date (according to Sir Nestor)

### Assessments / Mock Assessments
- [ ] Feature suggestion by Ms. Claire: add ability to use images on the questions of the assessments
- [ ] Head/Licensed teachers are the only ones who can conduct mock assessments. They must pass a certain point (TBD). Teacher conducting this should be randomized instead of the teacher who initially taught the student (to avoid bias results). Mock test and review sessions are conducted after doing all activities.
- [ ] Resources provided: video lessons, audio practice files, quizzes, writing tasks, live teacher booking option (if possible only, if student wants a teacher booked)

### Blockers

Items on this section require clarification as they may not be implemented

- [ ] Billing service to use is still in limbo and undecided.
- [ ] ~~If students picked a slot, it should show up on the teacher's calendar as well as on the student's~~. They may cancel but this incurs a penalty however this feature is useless at the moment since I need to know what kind of penalty is incurred for cancellation, otherwise this may not be implemented.
- [ ] Transcription packages for uploaded audios as requested by Ms. Claire requires a billing account to be used. Some of the packages I've researched so far are:
  - AssemblyAI (yes, with free trial. Costs $0.015/min after free credit)
  - Google Cloud Speech-to-Text (Free tier for the first 60 minutes/month for the first 12 months. Starts at \$0.006 per 15 seconds $1.44/hour)
  - Laravel Whisper (Self-hosted, no cost but the only cost is hosting resources. Best for offline use, no API dependency.)
  - OpenAI Whisper API via ```openai-php/laravel``` (not fully free)
