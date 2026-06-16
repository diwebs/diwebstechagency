<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diwebs Tech Agency - Web Setup Wizard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --dark-bg: #0d0f12;
            --dark-card: rgba(30, 33, 37, 0.65);
            --brand-teal: #008080;
            --brand-cyan: #00c2d1;
            --brand-white: #ffffff;
            --brand-gray: #94a3b8;
            --brand-red: #ef4444;
            --brand-green: #10b981;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--brand-white);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        /* Tech Grid Background */
        .tech-grid {
            position: fixed;
            inset: 0;
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(0, 194, 209, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 194, 209, 0.03) 1px, transparent 1px);
            z-index: -2;
        }

        .blur-sphere-1 {
            position: fixed;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vh;
            border-radius: 50%;
            background: rgba(0, 128, 128, 0.1);
            filter: blur(120px);
            z-index: -1;
        }

        .blur-sphere-2 {
            position: fixed;
            bottom: -10%;
            right: -10%;
            width: 50vw;
            height: 50vh;
            border-radius: 50%;
            background: rgba(0, 194, 209, 0.1);
            filter: blur(120px);
            z-index: -1;
        }

        /* Container */
        .installer-card {
            background: var(--dark-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 194, 209, 0.15);
            border-radius: 24px;
            width: 100%;
            max-width: 600px;
            padding: 2.5rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        /* Brand Title */
        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(to right, var(--brand-white), var(--brand-gray));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-name span {
            color: var(--brand-cyan);
            -webkit-text-fill-color: var(--brand-cyan);
        }

        /* Step Progress */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .progress-line {
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            height: 2px;
            background: rgba(255, 255, 255, 0.05);
            z-index: 0;
        }

        .progress-line-active {
            position: absolute;
            top: 20px;
            left: 0;
            width: 0%;
            height: 2px;
            background: linear-gradient(to right, var(--brand-teal), var(--brand-cyan));
            z-index: 0;
            transition: width 0.4s ease;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #15181c;
            border: 2px solid rgba(0, 194, 209, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            color: var(--brand-gray);
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .step-circle.active {
            background: var(--brand-cyan);
            color: #0d0f12;
            border-color: var(--brand-cyan);
            box-shadow: 0 0 15px rgba(0, 194, 209, 0.4);
        }

        .step-circle.completed {
            background: var(--brand-teal);
            color: var(--brand-white);
            border-color: var(--brand-teal);
        }

        /* Step Screens */
        .step-screen {
            display: none;
        }

        .step-screen.active {
            display: block;
            animation: fadeIn 0.4s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--brand-gray);
            margin-bottom: 2rem;
        }

        /* Requirements List */
        .req-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 2rem;
            max-height: 250px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .req-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.02);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.85rem;
        }

        .req-status.ok {
            color: var(--brand-green);
            font-weight: bold;
        }

        .req-status.fail {
            color: var(--brand-red);
            font-weight: bold;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--brand-cyan);
            margin-bottom: 0.5rem;
        }

        input, select {
            width: 100%;
            background: rgba(21, 24, 28, 0.8);
            border: 1px solid rgba(0, 194, 209, 0.15);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--brand-white);
            font-size: 0.85rem;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            border-color: var(--brand-cyan);
            box-shadow: 0 0 10px rgba(0, 194, 209, 0.15);
        }

        .row {
            display: grid;
            grid-template-cols: 1fr 1fr;
            gap: 1rem;
        }

        /* Buttons */
        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .btn {
            padding: 0.75rem 1.75rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            outline: none;
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid rgba(0, 194, 209, 0.2);
            color: var(--brand-cyan);
        }

        .btn-secondary:hover {
            background: rgba(0, 194, 209, 0.05);
        }

        .btn-primary {
            background: linear-gradient(to right, var(--brand-teal), var(--brand-cyan));
            border: none;
            color: #0d0f12;
            box-shadow: 0 4px 15px rgba(0, 194, 209, 0.25);
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fc8181;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-top: 1rem;
            display: none;
        }

        /* Loading Spinner */
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.1);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border-left-color: var(--brand-cyan);
            animation: spin 1s linear infinite;
            margin: 2rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 2rem auto;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.1);
            border: 2px solid var(--brand-green);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-green);
            font-size: 2.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 194, 209, 0.2);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 194, 209, 0.4);
        }
    </style>
</head>
<body>
    <div class="tech-grid"></div>
    <div class="blur-sphere-1"></div>
    <div class="blur-sphere-2"></div>

    <div class="installer-card">
        <!-- Brand Header -->
        <div class="brand">
            <svg class="brand-logo" viewBox="0 0 512 512" fill="none">
                <circle cx="256" cy="256" r="230" stroke="#00c2d1" stroke-width="32"/>
                <path d="M180 180 L256 120 L332 180 L300 380 L212 380 Z" fill="#008080"/>
            </svg>
            <div class="brand-name">Diwebs <span>Tech</span></div>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="progress-line"></div>
            <div class="progress-line-active" id="progress-line-active"></div>
            <div class="step-circle active" id="circle-1">1</div>
            <div class="step-circle" id="circle-2">2</div>
            <div class="step-circle" id="circle-3">3</div>
            <div class="step-circle" id="circle-4">4</div>
        </div>

        <!-- STEP 1: Requirements check -->
        <div class="step-screen active" id="screen-1">
            <h2>System Requirements</h2>
            <div class="subtitle">Verifying your server configurations are suitable to run the Diwebs platform.</div>
            
            <div class="req-list">
                @foreach ($requirements as $name => $ok)
                    <div class="req-item">
                        <span>{{ $name }}</span>
                        <span class="req-status {{ $ok ? 'ok' : 'fail' }}">
                            {{ $ok ? '✓ Active' : '✗ Failed' }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="actions">
                <div></div>
                <button class="btn btn-primary" onclick="goToStep(2)" {{ $isCompatible ? '' : 'disabled' }}>
                    Continue Setup
                </button>
            </div>
        </div>

        <!-- STEP 2: Database Settings -->
        <div class="step-screen" id="screen-2">
            <h2>Database Configuration</h2>
            <div class="subtitle">Specify your database parameters to initialize connectivity.</div>
            
            <form id="db-form" onsubmit="submitDatabase(event)">
                <div class="form-group">
                    <label for="db_connection">Database Driver</label>
                    <select id="db_connection" name="db_connection" onchange="toggleDbFields()">
                        <option value="sqlite">SQLite Database</option>
                        <option value="mysql">MySQL Database</option>
                    </select>
                </div>

                <div id="mysql-fields" style="display: none;">
                    <div class="row">
                        <div class="form-group">
                            <label for="db_host">Host IP/Domain</label>
                            <input type="text" id="db_host" name="db_host" value="127.0.0.1">
                        </div>
                        <div class="form-group">
                            <label for="db_port">Port</label>
                            <input type="number" id="db_port" name="db_port" value="3306">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label for="db_username">Database User</label>
                            <input type="text" id="db_username" name="db_username" value="root">
                        </div>
                        <div class="form-group">
                            <label for="db_password">Password</label>
                            <input type="password" id="db_password" name="db_password">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="db_database" id="db-name-label">Database Name / Path</label>
                    <input type="text" id="db_database" name="db_database" value="database/database.sqlite">
                </div>

                <div class="alert-error" id="db-error"></div>

                <div class="actions">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(1)">Back</button>
                    <button type="submit" class="btn btn-primary" id="db-submit-btn">Next Step</button>
                </div>
            </form>
        </div>

        <!-- STEP 3: Admin Configuration -->
        <div class="step-screen" id="screen-3">
            <h2>Create Admin Account</h2>
            <div class="subtitle">Set up the credentials for the super administrator account.</div>
            
            <form id="admin-form" onsubmit="submitAdmin(event)">
                <div class="form-group">
                    <label for="admin_name">Super Admin Name</label>
                    <input type="text" id="admin_name" name="name" placeholder="Diwebs Administrator" required>
                </div>

                <div class="form-group">
                    <label for="admin_email">Email Address</label>
                    <input type="email" id="admin_email" name="email" placeholder="admin@diwebstechagency.website" required>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="admin_password">Security Password</label>
                        <input type="password" id="admin_password" name="password" placeholder="Min. 12 characters" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password_confirmation">Confirm Password</label>
                        <input type="password" id="admin_password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <div class="alert-error" id="admin-error"></div>

                <div class="actions">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(2)">Back</button>
                    <button type="submit" class="btn btn-primary" id="admin-submit-btn">Finalize Installation</button>
                </div>
            </form>
        </div>

        <!-- STEP 4: Installation Process & Success -->
        <div class="step-screen" id="screen-4">
            <div id="install-processing">
                <div class="spinner"></div>
                <h2 style="text-align: center;">Installing Diwebs Ecosystem...</h2>
                <div class="subtitle" style="text-align: center; margin-top: 0.5rem;">Running database migrations, seeding default states, and configuring administration. Please wait...</div>
            </div>

            <div id="install-success" style="display: none; text-align: center;">
                <div class="success-checkmark">✓</div>
                <h2>Installation Successful!</h2>
                <div class="subtitle" style="margin-top: 0.5rem;">Your system is now ready for deployment. The development sandbox routes have been successfully blocked.</div>
                
                <button class="btn btn-primary" style="width: 100%; margin-top: 1rem;" onclick="redirectToLogin()">
                    Go to Portal Dashboard
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;

        function goToStep(step) {
            // Hide all screens
            document.querySelectorAll('.step-screen').forEach(s => s.classList.remove('active'));
            // Show new screen
            document.getElementById(`screen-${step}`).classList.add('active');

            // Manage circles progress
            document.querySelectorAll('.step-circle').forEach((c, idx) => {
                const cStep = idx + 1;
                c.classList.remove('active', 'completed');
                if (cStep === step) {
                    c.classList.add('active');
                } else if (cStep < step) {
                    c.classList.add('completed');
                }
            });

            // Set progress bar width
            const progressPercent = ((step - 1) / 3) * 100;
            document.getElementById('progress-line-active').style.width = `${progressPercent}%`;

            currentStep = step;
        }

        function toggleDbFields() {
            const driver = document.getElementById('db_connection').value;
            const mysqlFields = document.getElementById('mysql-fields');
            const nameLabel = document.getElementById('db-name-label');
            const nameInput = document.getElementById('db-database');

            if (driver === 'mysql') {
                mysqlFields.style.display = 'block';
                nameLabel.innerText = 'Database Schema Name';
                nameInput.value = 'diwebs_db';
            } else {
                mysqlFields.style.display = 'none';
                nameLabel.innerText = 'Database File Path';
                nameInput.value = 'database/database.sqlite';
            }
        }

        async function submitDatabase(e) {
            e.preventDefault();
            const form = document.getElementById('db-form');
            const submitBtn = document.getElementById('db-submit-btn');
            const errorDiv = document.getElementById('db-error');

            submitBtn.disabled = true;
            submitBtn.innerText = 'Validating Connection...';
            errorDiv.style.display = 'none';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('{{ route("install.database") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const res = await response.json();
                if (response.ok) {
                    goToStep(3);
                } else {
                    errorDiv.innerText = res.message || 'Database validation failed.';
                    errorDiv.style.display = 'block';
                }
            } catch (err) {
                errorDiv.innerText = 'Network error: could not connect to server.';
                errorDiv.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Next Step';
            }
        }

        async function submitAdmin(e) {
            e.preventDefault();
            const form = document.getElementById('admin-form');
            const submitBtn = document.getElementById('admin-submit-btn');
            const errorDiv = document.getElementById('admin-error');

            submitBtn.disabled = true;
            submitBtn.innerText = 'Finalizing...';
            errorDiv.style.display = 'none';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Advance to step 4 processing state immediately
            goToStep(4);

            try {
                const response = await fetch('{{ route("install.admin") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const res = await response.json();
                if (response.ok) {
                    document.getElementById('install-processing').style.display = 'none';
                    document.getElementById('install-success').style.display = 'block';
                    // Mark step 4 as completed
                    document.getElementById('circle-4').classList.add('completed');
                } else {
                    goToStep(3);
                    errorDiv.innerText = res.message || 'Super admin setup failed.';
                    errorDiv.style.display = 'block';
                }
            } catch (err) {
                goToStep(3);
                errorDiv.innerText = 'Network error during installation execution.';
                errorDiv.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Finalize Installation';
            }
        }

        function redirectToLogin() {
            window.location.href = '/secure-gate-admin';
        }
    </script>
</body>
</html>
