---
icon: heroicon-o-document-text
order: 1
---

# Introduction

Modules are one of the major features the system has. This is what the students will mainly use to kickstart their ESL journey and learn along the way. Think of modules as courses in college, where they take them and finish them before proceeding to the next. Once they complete all modules, they "graduate" from the system and use what they learn for other means. To find the admin panel for modules, look for **Modules** on the top navigation of your screen. If you're on mobile, you need to expand the hamburger menu on your left to find the Modules link. Afterwards, select Modules from the left side of the panel.

# Creating/Editing modules

The system will automatically determine the <ins>[slug](/admin/help/miscellaneous/terminologies#)</ins> of modules based on the <ins>[Name](#fields)</ins> field when created/edited.

- **Proficiency**: Enum classes. Determine the proficiency level the module will be available at. This is important as students who does not have the proficiency level cannot take the module. For example, if the module is for Advanced, students who does not have the Advanced proficiency cannot take it. However, this can be manually overriden when enrolling students
  - Options: Advanced, Beginner, Intermediate, Mastery, Pre-Intermediate, Upper-Intermediate
  - Required

- **Name**: Name of the module.
  - Max length: 128 characters
  - Min length: 5 characters
  - Required

- **Description**: A brief background on what the students can expect from this module. Markdown formatting is supported.
  - Min length: 5 characters
  - Required
