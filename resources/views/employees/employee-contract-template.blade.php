<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment Contract</title>
    <style>
        @page {
            margin: 0;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.5;
            width: 16cm;
            margin: 0 auto;
            padding: 1cm 1.8cm 2cm 1.8cm;
            color: #1a1a1a;
            background: white;
            font-size: 11pt;
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 20pt;
            border-bottom: 1px solid #000;
            padding-bottom: 8pt;
        }

        .section {
            margin-bottom: 16pt;
        }

        .placeholder {
            color: #000;
            font-weight: bold;
            border-bottom: 1px dotted #666;
            padding: 0 2pt;
        }

        .signature-section {
            margin-top: 24pt;
        }

        /* Replace grid with table */
        .signature-table {
            width: 100%;
            margin-top: 20pt;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 50%;
            padding-right: 12pt;
            vertical-align: top;
        }

        .signature-table td:last-child {
            padding-right: 0;
            padding-left: 12pt;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 28pt;
        }

        .label {
            font-weight: bold;
            margin-bottom: 4pt;
        }

        p {
            margin: 8pt 0;
            text-align: justify;
        }

        strong {
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="title">EMPLOYMENT CONTRACT</div>

    <div class="section">
        <p>This Employment Contract Agreement (hereinafter referred to as the "Agreement") was made and becomes effective on <span class="placeholder">{{$contract->start_date}}</span>,</p>
    </div>

    <div class="section">
        <p><strong>BY AND BETWEEN:</strong></p>
        <p><span class="placeholder">{{ $employer->first_name}} {{ $employer->last_name}}</span>, with an address of <span class="placeholder">
            {{ $employer_address->street }} {{ $employer_address->house_number }}
            @if ($employer_address->bus_number)
                - {{ $employer_address->bus_number }}
            @endif
            , {{ $employer_address->city->postcode }} {{ $employer_address->city->name }}, {{ $employer_address->city->country->name }}
            </span>, hereinafter referred to as the "Employer".</p>
    </div>

    <div class="section">
        <p><strong>AND:</strong></p>
        <p><span class="placeholder">{{ $employee->first_name}} {{ $employee->last_name}}</span>, with an address of <span class="placeholder">
            {{ $employee_address->street }} {{ $employee_address->house_number }}
            @if ($employee_address->bus_number)
                - {{ $employee_address->bus_number }}
            @endif
            , {{ $employee_address->city->postcode }} {{ $employee_address->city->name }}, {{ $employee_address->city->country->name }}
            </span>, hereinafter referred to as the "Employee", collectively referred to as the "Parties".</p>
    </div>

    <div class="section">
        <p><strong>DUTIES AND RESPONSIBILITIES</strong></p>
        <p>The Employee agrees to perform the following duties and responsibilities:</p>
        <p>- <span class="placeholder">{{$function->name}}</span></p>
    </div>

    <div class="section">
        <p><strong>TERMS OF AGREEMENT</strong></p>
        <p>This Agreement shall be effective on the date of signing this Agreement (hereinafter referred to as the "Effective Date") and shall continue until terminated in accordance with the provisions set forth herein.</p>
    </div>

    <div class="signature-section">
        <p><strong>SIGNATURE AND DATE</strong></p>
        <p>The Parties hereby agree to the terms and conditions set forth in this Agreement, and such is demonstrated throughout by their signatures below:</p>

        <table class="signature-table">
            <tr>
                <td>
                    <div class="label">EMPLOYER</div>
                    <div class="label">Name</div>
                    <div class="signature-line"></div>
                    <div class="label" style="margin-top: 14pt;">Signed (signature) and Date</div>
                </td>
                <td>
                    <div class="label">EMPLOYEE</div>
                    <div class="label">Name</div>
                    <div class="signature-line"></div>
                    <div class="label" style="margin-top: 14pt;">Signed (signature) and Date</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>