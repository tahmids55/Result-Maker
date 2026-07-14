# ResultMaker — Dynamic Result Management System

> **One system adapts to ANY school's exam pattern. Design marksheets in MS Word, upload, and generate automatically.**

---

## 📋 Table of Contents

1. [Features](#features)
2. [Tech Stack](#tech-stack)
3. [System Requirements](#system-requirements)
4. [Installation](#installation)
5. [Configuration](#configuration)
6. [Usage Guide](#usage-guide)
7. [Marksheet Template Guide](#marksheet-template-guide)
8. [Queue & Background Jobs](#queue--background-jobs)
9. [OCR Setup](#ocr-setup)
10. [SMS / WhatsApp Setup](#sms--whatsapp-setup)
11. [Testing](#testing)
12. [Project Structure](#project-structure)
13. [Sample Workflow](#sample-workflow)

---

## ✨ Features

| Module | Description |
|--------|-------------|
| 🏫 Class & Section Management | Hierarchical class-section structure with bulk CSV import |
| 👥 Student Management | Full CRUD, photo upload with auto-resize, CSV bulk import, search & filter |
| 📚 Subject Management | Dynamic exam components (MCQ, CQ, Practical) & Optional/4th Subject logic |
| 📝 Exam Management | Multiple exams per year, activate/deactivate, date ranges |
| ✏️ Marks Entry | Spreadsheet-style entry with real-time GPA calculation via Livewire |
| 📊 Result Analytics | Class result sheet, merit list, grade distribution charts, CSV export |
| 📄 Template Editing | Built-in ONLYOFFICE editor with one-click placeholder insertion plugin |
| 🖨️ Marksheet Generation | Fill templates with student data & photos, download ZIP or individual .docx |
| 🔍 OCR Import | Upload photos of mark sheets, extract data with Tesseract OCR |
| 💬 SMS / WhatsApp | Bulk result notifications via Twilio (SMS) or WhatsApp Business API |
| ⚙️ Backups & Settings | School profile, standard BD grading system, automated MEGA cloud backups |

---

## 🛠️ Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL 8.0
- **Frontend:** Livewire 3 + Alpine.js + TailwindCSS (CDN)
- **Word Processing:** PHPOffice PHPWord
- **PDF:** DomPDF / PHPWord-to-PDF
- **OCR:** Tesseract OCR (thiagoalessio/tesseract_ocr)
- **Queue:** Laravel Horizon (Redis)
- **SMS:** Twilio API
- **Charts:** Chart.js (CDN)

---

## 💻 System Requirements

- PHP 8.2+
- MySQL 8.0+
- Redis (for queues)
- Tesseract OCR (for OCR feature)
- Composer 2.x
- Node.js 18+ & NPM (for assets, optional with CDN)

### PHP Extensions Required
```
php-mbstring php-xml php-zip php-mysql php-curl php-dom php-gd php-bcmath
```

---

## 🚀 Installation

### Step 1 — Clone the Repository
```bash
git clone https://github.com/yourorg/ResultMaker.git
cd ResultMaker
```

### Step 2 — Install PHP Dependencies
```bash
composer install
```

### Step 3 — Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### Step 4 — Configure Database
Edit `.env`:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ResultMaker
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:
```sql
CREATE DATABASE ResultMaker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 5 — Run Migrations & Seed
```bash
php artisan migrate --seed
```

This creates:
- Admin account: `admin@ResultMaker.com` / `password`
- Sample school, classes, students, subjects, exams, marks

### Step 6 — Storage Link
```bash
php artisan storage:link
```

### Step 7 — Start the Application
```bash
php artisan serve
```

Visit: http://localhost:8000

---

## ⚙️ Configuration

### .env Key Variables

```env
# Application
APP_URL=http://localhost
APP_TIMEZONE=Asia/Dhaka

# Database
DB_CONNECTION=mysql
DB_DATABASE=ResultMaker

# Queue (Redis for background jobs)
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Twilio SMS
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_auth_token
TWILIO_FROM=+1234567890

# WhatsApp (via Twilio)
# Use same Twilio credentials, channel handled automatically

# Tesseract OCR
TESSERACT_PATH=/usr/bin/tesseract
TESSERACT_LANG=eng+ben
```

### Automated MEGA Cloud Backups
ResultMaker includes a `backup.sh` script that automatically dumps the database, compresses it, and syncs it to a MEGA cloud account using `mega-cmd`.
1. Install `mega-cmd` on your server.
2. Login: `mega-login your@email.com password`
3. Add to your server's cron (`crontab -e`):
```cron
0 2 * * * /path/to/ResultMaker/backup.sh >> /var/backups/resultmaker/logs/backup.log 2>&1
```

---

## 📖 Usage Guide

### 1. Initial Setup
1. Log in at `/login` with `admin@ResultMaker.com` / `password`
2. Go to **Settings** → Enter your school name, address, logo
3. Configure the grading system (default: Bangladesh SSC scale)

### 2. Create Classes & Sections
- Go to **Classes** → Add Class (e.g., "Class 10")
- Go to **Sections** → Add Section → Select class → Enter name (e.g., "A", "Science")
- Or bulk import via CSV: `Class Name, Section Name`

### 3. Add Students
- Go to **Students** → Add Student → Fill form
- Or bulk CSV import:
  ```
  name,roll,father_name,mother_name,class_name,section_name,session
  Rakib Hasan,101,Abdul Hasan,Fatema Begum,Class 10,A,2024-2025
  ```

### 4. Add Subjects with Components
- Go to **Subjects** → Add Subject
- Select class + section
- Add exam components dynamically:
  - MCQ: Full 40, Pass 16
  - CQ: Full 60, Pass 24
  - Practical: Full 25, Pass 13
- Use quick presets or build custom components

### 5. Create Exam
- Go to **Exams** → Create Exam
- Set name, year, dates
- Click "Active" toggle to make it the current exam

### 6. Enter Marks
- Go to **Marks Entry**
- Select Class → Section → Exam → Click "Load Marks"
- Spreadsheet table appears with all students × subjects × components
- Type marks directly into cells
- Real-time GPA/Grade calculated as you type
- Click "Save All Marks" when done

### 7. View Results
- Go to **Results**
- Select Class + Section + Exam → "View Results"
- See ranked result sheet with charts
- Export to CSV for Excel/printing

### 8. Generate Marksheets
- First upload a template (see Template Guide below)
- Go to **Templates** to edit the template directly in the browser using the ONLYOFFICE integration. Use the custom plugin sidebar to cleanly insert placeholders without breaking formatting.
- Go to **Marksheets**
- Select Class + Section + Exam + Template
- **Small classes (≤30):** Click "Generate & Download ZIP" — instant download
- **Large classes (50+):** Click "Queue Generation" — processes in background via Horizon

### 9. Grading Logic (Bangladesh Standard)
ResultMaker natively supports the Bangladesh education board rules for the **4th / Optional Subject**:
- The optional subject is excluded from the average GPA divisor.
- A student receives a bonus of `max(0, GPA - 2.0)` added to their total GPA.
- A student receives a bonus of `max(0, marks - 40)` added to their total obtained marks.
- Failing the optional subject results in `0` bonus and explicitly does **not** fail the student for the exam.

---

## 📄 Marksheet Template Guide

### Creating Your Template in MS Word

1. Open Microsoft Word
2. Design your marksheet layout (school logo, headers, table, signature areas)
3. Use `{{placeholder_name}}` syntax for dynamic data:

```
Student Name: {{st_name}}
Roll No: {{roll}}
Father's Name: {{f_name}}
Class: {{cls}}    Section: {{sec}}
Exam: {{exam_name}} {{exam_year}}

Total Marks: {{tot_mks}} / {{fl_mks}}
Percentage: {{pct}}
GPA: {{gpa}}
Grade: {{grd}}
Result: {{status}}
Merit Position: {{rank}}
Photo: {{st_photo}}

School: {{sch_name}}
Generated: {{gen_dt}}
```

4. For subject-wise marks, use: `{{subject_mathematics_obtained}}`, `{{subject_mathematics_grade}}`
5. Save as `.docx`

### Available Placeholders

| Placeholder | Value |
|-------------|-------|
| `{{st_name}}` | Full name of student |
| `{{st_photo}}` | Profile photo of student |
| `{{dob}}` | Date of Birth |
| `{{roll}}` | Roll number |
| `{{reg_no}}` | Registration number |
| `{{f_name}}` | Father's name |
| `{{m_name}}` | Mother's name |
| `{{cls}}` | Class name |
| `{{sec}}` | Section name |
| `{{sess}}` | Academic session |
| `{{exam_name}}` | Exam name |
| `{{exam_year}}` | Exam year |
| `{{tot_mks}}` | Total obtained marks |
| `{{fl_mks}}` | Total full marks |
| `{{pct}}` | Percentage (e.g., 87.50%) |
| `{{gpa}}` | GPA (e.g., 5.00) |
| `{{grd}}` | Grade (A+, A, B...) |
| `{{div}}` | Division (First Division...) |
| `{{status}}` | PASSED or FAILED |
| `{{rank}}` | Merit rank |
| `{{sch_name}}` | School name |
| `{{ftr_txt}}` | Footer text from settings |
| `{{gen_dt}}` | Date of generation |
| `{{subject_math_obt}}` | Subject marks (replace math with slug) |

### Uploading & Mapping

1. Go to **Templates** → Upload Template
2. System detects all `{{placeholders}}`
3. Map each placeholder to the correct database field
4. Set as default template

---

## ⚡ Queue & Background Jobs

### Starting the Queue Worker
```bash
# Simple queue worker
php artisan queue:work

# With Laravel Horizon (recommended for production)
php artisan horizon
```

### Jobs
| Job | Description |
|-----|-------------|
| `GenerateMarksheetJob` | Generates one student's marksheet from template |
| `ProcessOCRJob` | Runs Tesseract OCR on an uploaded image |
| `SendSMSJob` | Sends SMS or WhatsApp message via Twilio |

---

## 🔍 OCR Setup

### Install Tesseract
```bash
# Ubuntu/Debian
sudo apt-get install tesseract-ocr

# For Bengali support
sudo apt-get install tesseract-ocr-ben

# Verify installation
tesseract --version
```

### Configure in .env
```env
TESSERACT_PATH=/usr/bin/tesseract
TESSERACT_LANG=eng+ben
```

### Using OCR
1. Go to **OCR Import**
2. Upload a JPG/PNG photo of a student list or answer sheet
3. Select language (English, Bengali, or both)
4. Click "Start OCR Processing"
5. Review extracted data in the editable table
6. Assign to exam + subject
7. Click "Save Marks"

---

## 💬 SMS / WhatsApp Setup

### Twilio Setup
1. Create account at [twilio.com](https://twilio.com)
2. Get Account SID, Auth Token, and a phone number
3. Add to `.env`:
```env
TWILIO_SID=ACxxxxx
TWILIO_TOKEN=xxxxx
TWILIO_FROM=+1234567890
```

### WhatsApp (via Twilio Sandbox)
1. Go to Twilio Console → WhatsApp Sandbox
2. Use same credentials — the app handles `whatsapp:` prefix automatically

### Message Template Variables
```
{student_name}, {roll}, {exam_name}, {exam_year},
{grade}, {gpa}, {total}, {percentage}, {status}
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature

# With coverage report
php artisan test --coverage
```

### Tests Included
- `ResultCalculationServiceTest` — GPA calculation, pass/fail, rank assignment
- `MarksEntryTest` — Authentication, mark storage, validation

---

## 📁 Project Structure

```
ResultMaker/
├── app/
│   ├── Console/Commands/
│   │   ├── BackupDatabase.php
│   │   └── RestoreDatabase.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── ClassController.php
│   │   │   ├── SectionController.php
│   │   │   ├── StudentController.php
│   │   │   ├── SubjectController.php
│   │   │   ├── ExamController.php
│   │   │   ├── MarksheetController.php
│   │   │   ├── MarksheetTemplateController.php
│   │   │   ├── OcrController.php
│   │   │   ├── ResultController.php
│   │   │   ├── SmsController.php
│   │   │   └── SettingsController.php
│   │   └── Livewire/
│   │       └── MarksEntry.php        ← Spreadsheet marks entry
│   ├── Jobs/
│   │   ├── GenerateMarksheetJob.php
│   │   ├── ProcessOCRJob.php
│   │   └── SendSMSJob.php
│   ├── Models/
│   │   ├── SchoolClass.php
│   │   ├── Section.php
│   │   ├── Student.php
│   │   ├── Subject.php
│   │   ├── Exam.php
│   │   ├── Mark.php
│   │   ├── Result.php
│   │   ├── MarksheetTemplate.php
│   │   ├── GeneratedMarksheet.php
│   │   ├── OcrImport.php
│   │   ├── SmsLog.php
│   │   ├── GradeConfig.php
│   │   └── School.php
│   └── Services/
│       ├── ResultCalculationService.php  ← Core GPA/grade logic
│       ├── MarksheetGenerationService.php ← PHPWord template filling
│       ├── OcrService.php
│       └── SmsService.php
├── database/
│   ├── migrations/                    ← 13 migration files
│   └── seeders/DatabaseSeeder.php     ← Demo school + data
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/login.blade.php
│   ├── dashboard/
│   ├── classes/ sections/ students/ subjects/
│   ├── exams/ marks/ results/
│   ├── templates/ marksheets/
│   ├── ocr/ sms/ settings/
│   └── livewire/marks-entry.blade.php
├── routes/web.php
├── config/services.php
└── tests/
    ├── Unit/ResultCalculationServiceTest.php
    └── Feature/MarksEntryTest.php
```

---

## 🎯 Sample Workflow

```
1.  Login           →  admin@ResultMaker.com / password
2.  Settings        →  Set school name, logo, grading
3.  Classes         →  Add "Class 10"
4.  Sections        →  Add "A" under Class 10
5.  Students        →  Add Rakib Hasan, Roll 101 (or import CSV)
6.  Subjects        →  Add Mathematics: MCQ(40/16) + CQ(60/24)
7.  Exams           →  Create "First Term 2024" → set Active
8.  Marks Entry     →  Class 10 → A → First Term
                       Enter 35 (MCQ), 52 (CQ) → sees GPA 5.00, A+
                       Click Save
9.  Results         →  View Class 10A result sheet with rank
10. Templates       →  Upload marksheet.docx → map placeholders
11. Marksheets      →  Generate ZIP for Class 10A → download
12. OCR             →  Upload photo → review extracted data
13. SMS             →  Send bulk result to all parents
```

---

## 🔒 Security Notes

- All routes require authentication via `auth` middleware
- CSRF protection on all forms
- SQL injection prevented via Eloquent ORM
- File uploads validated (type + size)
- Marks validated against full_marks on save
- XSS prevention via Blade's `{{ }}` auto-escaping

---

## 📝 License

MIT License — Free for educational and commercial use.

---

*Built with ❤️ for schools that deserve better software.*
