<h1 align="center">Never Give Up World — NGO Management System</h1>

<p align="center">
A <strong>Core PHP-based Web Application</strong> for a <strong>Social Impact NGO</strong> — managing community programs, donations, enrollments, volunteers, and stories through a secure <strong>Admin Dashboard</strong> and a dynamic public website.
</p>

<hr>

<h2>🎯 Overview</h2>

<p>
Never Give Up World empowers lives through education, skill development, and community support. This platform digitizes the NGO's complete operational lifecycle — from <strong>public engagement → program enrollment → donation collection → volunteer onboarding → impact tracking</strong> — replacing manual coordination with a single, transparent digital workflow.
</p>

<p>
Built with strict <strong>authentication and role-based session management</strong>, ensuring that sensitive data (donor details, applicant info) is securely managed by authorized administrators.
</p>

<hr>

<h2>🧩 Panels &amp; Key Modules</h2>

<h3>1️⃣ Public Website — Community Front</h3>
<ul>
    <li><strong>About &amp; Impact</strong> — mission, vision, and real-time impact statistics</li>
    <li><strong>Program Enrollment</strong> — dynamic forms for Skill Development, Capacity Building, and Happiness Coaching</li>
    <li><strong>Donation Portal</strong> — secure donation submission with UTR tracking</li>
    <li><strong>Volunteer Registration</strong> — onboarding for community helpers</li>
    <li><strong>Storytelling</strong> — published success stories and NGO updates</li>
    <li><strong>Contact &amp; Partners</strong> — inquiry forms and partnership applications</li>
</ul>

<h3>2️⃣ Admin Panel — Control Tower</h3>
<ul>
    <li><strong>Analytics Dashboard</strong> — visual charts (Chart.js) for donations, enrollments, and stories</li>
    <li><strong>Story Management</strong> — create, edit, and publish blog posts/stories with featured images</li>
    <li><strong>Donation Tracking</strong> — view donor details, amounts, payment methods, and UTR numbers</li>
    <li><strong>Enrollment Management</strong> — tabbed views for Skill, Capacity, and Happiness programs</li>
    <li><strong>Volunteer Directory</strong> — manage volunteer availability and areas of interest</li>
    <li><strong>Communication Hub</strong> — contact messages, inquiries, and partner requests</li>
    <li><strong>Settings &amp; Users</strong> — secure admin account management and site configuration</li>
</ul>

<hr>

<h2>🔄 Business Flow</h2>

<pre>
Public User          →  Website Form         →  Database             →  Admin Dashboard
─────────────────────────────────────────────────────────────────────────────────────────
Views Programs       Submits Enrollment        Stores Applicant Data  Reviews Enrollments
Makes Donation       Uploads Payment Proof     Records Transaction    Verifies Donations
Wants to Volunteer   Fills Volunteer Form      Saves Info             Manages Volunteers
Reads Stories        Submits Contact Msg       Stores Message         Replies & Updates
</pre>

<p>
Each submission is securely stored in MySQL and immediately visible to the Admin, creating a transparent, auditable workflow from public engagement to organizational impact.
</p>

<hr>

<h2>💡 Business Impact</h2>
<ul>
    <li><strong>Centralized Operations</strong> — all inquiries, donations, and enrollments in one secure platform</li>
    <li><strong>Data-Driven Decisions</strong> — real-time analytics help the NGO understand donor behavior and program reach</li>
    <li><strong>Secure Data Handling</strong> — PDO prepared statements and password hashing protect sensitive community data</li>
    <li><strong>Financial Visibility</strong> — track donation UTRs, amounts, and donor history effortlessly</li>
    <li><strong>Zero Manual Coordination</strong> — automated data capture eliminates the need for manual spreadsheets</li>
</ul>

<hr>

<h2>🚀 Key Highlights</h2>
<ul>
    <li><strong>Responsive Frontend</strong> — fully mobile-friendly UI using Bootstrap 5</li>
    <li><strong>Modern Admin Dashboard</strong> — colorful KPI cards, sidebar navigation, and interactive charts</li>
    <li><strong>Secure Authentication</strong> — session management, CSRF protection, and password hashing</li>
    <li><strong>Dynamic DataTables</strong> — server-side ready pagination, search, and sorting for heavy data tables</li>
    <li><strong>Interactive Modals</strong> — smooth popup interfaces for viewing details without page reloads</li>
</ul>

<hr>

<h2>🖼️ System Preview</h2>

<table>
    <tr>
        <td align="center" width="50%">
            <strong>Admin Dashboard Overview</strong><br><br>
            <img src="screensort/Admin%20Panel%20dashboard.png" alt="Admin Dashboard" width="400" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </td>
        <td align="center" width="50%">
            <strong>Frontend Home Page</strong><br><br>
            <img src="screensort/Home%20page.png" alt="Home Page" width="400" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </td>
    </tr>
    <tr>
        <td align="center" width="50%">
            <strong>Admin Panel (Management View)</strong><br><br>
            <img src="screensort/Admin%20panel.png" alt="Admin Panel" width="400" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </td>
        <td align="center" width="50%">
            <strong>Frontend About Us</strong><br><br>
            <img src="screensort/About%20us.png" alt="About Us Page" width="400" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </td>
    </tr>
</table>

<hr>

<h2>🛠️ Tech Stack</h2>

<p>
<strong>PHP</strong> · <strong>MySQL</strong> · <strong>PDO</strong> · <strong>HTML5</strong> · <strong>CSS3</strong> · <strong>Bootstrap 5</strong> · <strong>JavaScript</strong> · <strong>jQuery</strong> · <strong>AJAX</strong> · <strong>Chart.js</strong> · <strong>DataTables</strong>
</p>

<hr>

<h2>👨‍💻 Contributions</h2>
<ul>
    <li>Architected and developed the <strong>complete Admin Panel</strong> and public-facing web application.</li>
    <li>Designed the <strong>database schema</strong> and implemented secure PDO prepared statements.</li>
    <li>Built <strong>responsive UI components</strong> (KPI cards, DataTables, modals, sidebars) using Bootstrap 5.</li>
    <li>Implemented <strong>CRUD operations</strong> for stories, enrollments, donations, and volunteers.</li>
    <li>Integrated <strong>Chart.js analytics</strong> to visualize donation trends and program distribution.</li>
</ul>

<hr>

<h2>📌 Project Documentation</h2>

<p>
📑 <strong>Download:</strong> <a href="docs/NeverGiveUpWorld-Project-Doc.pdf" target="_blank">Download Full Project Documentation (PDF)</a>
</p>
