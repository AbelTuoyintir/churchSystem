<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $messageModel->subject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #111827;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
            font-size: 16px;
        }
        .greeting {
            font-weight: 600;
            margin-bottom: 15px;
        }
        .message-body {
            white-space: pre-line;
            margin-bottom: 25px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'Church Management') }}</h1>
        </div>
        <div class="content">
            <div class="greeting">Dear {{ $person->first_name ?: 'Member' }},</div>
            <div class="message-body">
                {!! nl2br(e($messageModel->body)) !!}
            </div>
            <p>Blessings,<br><strong>{{ $messageModel->createdBy?->name ?? 'Church Leadership' }}</strong></p>
        </div>
        <div class="footer">
            <p>You received this message because you are a registered member of {{ config('app.name', 'our church') }}.</p>
        </div>
    </div>
</body>
</html>
