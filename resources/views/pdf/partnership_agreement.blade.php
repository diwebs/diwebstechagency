<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Terms of Partnership - Diwebs Tech Agency</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #111827;
            line-height: 1.5;
            padding: 30px;
            font-size: 11pt;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #0d9488;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 10pt;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
            margin-top: 15px;
            text-transform: uppercase;
            text-align: center;
        }
        .agreement-intro {
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: justify;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0d9488;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 3px;
        }
        p, li {
            margin-bottom: 10px;
            text-align: justify;
        }
        ul {
            padding-left: 20px;
            margin-bottom: 15px;
        }
        .grid-signatures {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .grid-signatures td {
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .signature-box {
            border-bottom: 1px solid #9ca3af;
            margin-top: 30px;
            padding-bottom: 5px;
            min-height: 35px;
        }
        .signature-font {
            font-family: 'Courier New', Courier, monospace;
            font-style: italic;
            font-size: 14pt;
            font-weight: bold;
            color: #0d9488;
        }
        .stamp-box {
            border: 2px dashed #0d9488;
            color: #0d9488;
            display: inline-block;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 11pt;
            margin-top: 10px;
            text-transform: uppercase;
            border-radius: 4px;
        }
        .footer {
            margin-top: 50px;
            font-size: 8pt;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">DIWEBS TECH AGENCY</div>
        <div class="subtitle">Ecosystem Partner Program</div>
        <div class="title">MUTUAL PARTNERSHIP AGREEMENT</div>
    </div>
    
    <div class="agreement-intro">
        <p>This Mutual Partnership Agreement (the "Agreement") is entered into and made effective as of <strong>{{ $date }}</strong>, by and between:</p>
        <p><strong>DIWEBS TECH AGENCY</strong>, with its principal operations center (hereinafter referred to as the <strong>"Agency"</strong>), and</p>
        <p><strong>{{ $company_name }}</strong>, with website/corporate reference <strong>{{ $website ?? 'N/A' }}</strong>, represented by its authorized representative <strong>{{ $signed_name }}</strong> (hereinafter referred to as the <strong>"Partner"</strong>).</p>
    </div>
    
    <div class="section-title">1. Purpose & Objectives</div>
    <p>The Agency and the Partner agree to establish a strategic partnership to collaborate on technology development, digital transformation initiatives, and business growth opportunities. The parties will cooperate to deliver high-quality technology solutions, leverage mutual strengths, and expand market access.</p>
    
    <div class="section-title">2. Scope of Collaboration</div>
    <p>The collaboration will encompass the following areas:</p>
    <ul>
        <li><strong>Technology Development:</strong> Referral of software development, mobile application design, and system integration services.</li>
        <li><strong>Resource Sharing:</strong> Exchanging professional insights, technical guidelines, and optimization best practices.</li>
        <li><strong>Joint Marketing:</strong> Co-branding promotional activities, case study presentations, and market segment collaborations.</li>
    </ul>
    
    <div class="section-title">3. Commitments and Responsibilities</div>
    <p>Each party agrees to dedicate appropriate resources, personnel, and efforts to ensure the successful execution of joint initiatives. Both parties commit to communicating professionally, delivering services with excellence, and protecting the reputation of the partnership.</p>
    
    <div class="section-title">4. Confidentiality and Intellectual Property</div>
    <p>Both parties agree to hold in strict confidence all proprietary information, software codebase, client lists, and business strategies disclosed during the partnership. Each party retains sole ownership of its pre-existing intellectual property. Any intellectual property generated jointly will be governed by separate, project-specific Statement of Works (SOWs).</p>
    
    <div class="section-title">5. Terms of Agreement and Termination</div>
    <p>This Agreement shall remain in force for a period of one (1) year from the effective date and shall automatically renew unless terminated. Either party may terminate this partnership by providing thirty (30) days written notice to the other party.</p>
    
    <div class="section-title">6. Governing Law</div>
    <p>This Agreement and all associated project execution terms shall be governed by and construed in accordance with the applicable laws of the jurisdiction of operations of Diwebs Tech Agency.</p>
    
    <table class="grid-signatures">
        <tr>
            <td>
                <strong>For Diwebs Tech Agency:</strong>
                <div class="signature-box">
                    <span class="signature-font">/s/ Jude Carter</span>
                </div>
                <div class="stamp-box">OFFICIAL PARTNER VALIDATED</div>
                <br><br>
                <strong>Name:</strong> Jude Carter
                <br>
                <strong>Title:</strong> Director of Partner Relations
                <br>
                <strong>Date:</strong> {{ $date }}
            </td>
            <td>
                <strong>For the Partner ({{ $company_name }}):</strong>
                <div class="signature-box">
                    <span class="signature-font">/s/ {{ $signed_name }}</span>
                </div>
                <div class="stamp-box">PARTNER E-SIGNED</div>
                <br><br>
                <strong>Name:</strong> {{ $signed_name }}
                <br>
                <strong>Title:</strong> {{ $partnership_type }} Partner / Rep
                <br>
                <strong>Date:</strong> {{ $date }}
            </td>
        </tr>
    </table>

    <div class="footer">
        Diwebs Tech Agency Digital Onboarding Workspace • Professional Agreement Ref: DIW-PRT-{{ strtoupper(substr(md5($company_name . $date), 0, 8)) }}
    </div>
</body>
</html>
