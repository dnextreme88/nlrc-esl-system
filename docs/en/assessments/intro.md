---
icon: heroicon-o-pencil-square
order: 1
---

Assessments are a great way to evaluate your students' performance and learning journey by letting them answer a series of questions. To find the admin panel for assessments, look for **Assessments** on the top navigation of your screen. If you're on mobile, you need to expand the hamburger menu on your left to find the Assessments link.

# Creating/Editing assessments

The system will automatically determine the <ins>[slug](/admin/help/miscellaneous/terminologies#)</ins> of assessments based on the <ins>[Title](#fields)</ins> field when created/edited.

- **Title**: Name of the assessment.
  - Max length: 128 characters
  - Min length: 5 characters
  - Required

- **Type**: Enum classes. Used to classify the kind of assessment the students will be taking.
  - Options: Exercise, Mock Assessment, and Warm-up Activity
  - Required

- **Description**: A brief background on what the students can expect from this assessment. Markdown formatting is supported.
  - Min length: 5 characters
  - Required

- **Is active**: Determines the availability of assessments to students. Toggle this on so that this assessment can be assigned to units, otherwise turn this off if you don't want it selected by any unit.

---

# Choices and Questions

You can only add questions and choices when the assessment is created. You may add multiple choices to a single question but not multiple questions at a given time.

- **Title**: Label of the question
  - Min length: 5 characters
  - Required
  - Extra validations:
    - Each question must have at least 1 correct answer
    - Each question cannot have all choices as correct answers

- **Choices**: Options that the student can pick from. **Content** should be the label that appears to the student. **is correct answer?** is a special field that determines which choices are correct and how they are displayed in the assessment. If there is only 1 choice whose value is set to Yes, the choices will show as radio boxes, whereas 2 or more choices marked as correct answers will show up as checkboxes. In addition to multiple correct answers, the student will be limited to only pick those same number of choices for this question. For example, if a question has 5 choices, and 3 of which are marked correct answers, the student can only pick a maximum of 3 answers for that question.
  - Min choices: 2
  - Max choices: 8

Once you're done adding the choices and questions, you may now <ins>[attach the assessment to a unit](/admin/help/assessments/attaching-assessments-to-units)</ins>.

# Deleting assessments

You can only delete assessments if you delete all questions on that assessment.

## Answer Keys

There will be a link called **View answer keys** next to a question which displays all correct choices for that question. This only shows up once a question has been created. When you click this link, a modal shows up and anything highlighted in green color are marked as correct choices.
