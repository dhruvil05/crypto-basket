<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Rejected - {{ config('app.name') }}</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; line-height: 1.6;">

    <!-- Email Container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background-color: #f8f9fa;">
        <tr>
            <td style="padding: 40px 20px;">

                <!-- Main Email Card -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                    style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 600;">
                                {{ config('app.name') }}</h1>
                        </td>
                    </tr>

                    <!-- Alert Banner -->
                    <tr>
                        <td
                            style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 0;">
                            <div style="display: flex; align-items: center;">
                                <div style="color: #856404; font-size: 18px; margin-right: 10px;">⚠️</div>
                                <div style="color: #856404; font-weight: 600; font-size: 16px;">Transaction Update
                                    Required</div>
                            </div>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <!-- Greeting -->
                            <h2 style="color: #2c3e50; margin: 0 0 20px 0; font-size: 24px; font-weight: 600;">Dear
                                {{ $transaction->user->name }},</h2>

                            <p style="color: #555555; font-size: 16px; margin: 0 0 25px 0;">
                                We regret to inform you that your recent wallet deposit has been declined and requires
                                your attention.
                            </p>

                            <!-- Transaction Details Card -->
                            <div
                                style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 25px; margin: 25px 0;">
                                <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px; font-weight: 600;">📋
                                    Transaction Details</h3>

                                <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                    width="100%">
                                    <tr>
                                        <td style="padding: 8px 0; color: #6c757d; font-weight: 500; width: 40%;">
                                            Amount:</td>
                                        <td style="padding: 8px 0; color: #2c3e50; font-weight: 600; font-size: 18px;">
                                            ₹{{ number_format($transaction->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #6c757d; font-weight: 500;">Transaction ID:
                                        </td>
                                        <td
                                            style="padding: 8px 0; color: #2c3e50; font-family: monospace; font-size: 14px;">
                                            {{ $transaction->id ?? $transaction->id }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #6c757d; font-weight: 500;">Date:</td>
                                        <td style="padding: 8px 0; color: #2c3e50;">
                                            {{ $transaction->created_at->format('M d, Y \a\t h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #6c757d; font-weight: 500;">Status:</td>
                                        <td style="padding: 8px 0;">
                                            <span
                                                style="background-color: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;">🚫
                                                REJECTED</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @if ($transaction->admin_comment)
                                <!-- Rejection Reason Card -->
                                <div
                                    style="background-color: #f8d7da; border: 1px solid #f5c6cb; border-left: 4px solid #dc3545; border-radius: 8px; padding: 20px; margin: 25px 0;">
                                    <h3 style="color: #721c24; margin: 0 0 10px 0; font-size: 16px; font-weight: 600;">❌
                                        Reason for Rejection</h3>
                                    <p style="color: #721c24; margin: 0; font-size: 15px;">
                                        {{ $transaction->admin_comment }}</p>
                                </div>
                            @endif

                            <!-- What's Next Section -->
                            <div style="margin: 30px 0;">
                                <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 20px; font-weight: 600;">🔄
                                    What happens next?</h3>
                                <p style="color: #555555; margin: 0 0 20px 0;">Don't worry - your funds are safe. Here
                                    are your options:</p>

                                <div style="margin: 20px 0;">
                                    <div style="display: flex; align-items: flex-start; margin: 15px 0;">
                                        <div
                                            style="background-color: #28a745; color: white; border-radius: 50%; width: 24px; height: 24px; text-align: center; line-height: 24px; font-size: 12px; font-weight: bold; margin-right: 15px; flex-shrink: 0;">
                                            1</div>
                                        <div>
                                            <strong style="color: #2c3e50;">Try Again:</strong>
                                            <span style="color: #6c757d;"> You can initiate a new deposit with the
                                                correct information</span>
                                        </div>
                                    </div>

                                    <div style="display: flex; align-items: flex-start; margin: 15px 0;">
                                        <div
                                            style="background-color: #007bff; color: white; border-radius: 50%; width: 24px; height: 24px; text-align: center; line-height: 24px; font-size: 12px; font-weight: bold; margin-right: 15px; flex-shrink: 0;">
                                            2</div>
                                        <div>
                                            <strong style="color: #2c3e50;">Contact Support:</strong>
                                            <span style="color: #6c757d;"> If you believe this was processed in
                                                error</span>
                                        </div>
                                    </div>

                                    <div style="display: flex; align-items: flex-start; margin: 15px 0;">
                                        <div
                                            style="background-color: #ffc107; color: #212529; border-radius: 50%; width: 24px; height: 24px; text-align: center; line-height: 24px; font-size: 12px; font-weight: bold; margin-right: 15px; flex-shrink: 0;">
                                            3</div>
                                        <div>
                                            <strong style="color: #2c3e50;">Check Requirements:</strong>
                                            <span style="color: #6c757d;"> Review our deposit guidelines to ensure
                                                compliance</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div style="text-align: center; margin: 35px 0;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                    style="margin: 0 auto;">
                                    <tr>
                                        <td style="padding: 0 10px;">
                                            <a href="#"
                                                style="display: inline-block; background: linear-gradient(135deg, #28a745, #20c997); color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 25px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); transition: all 0.3s ease;">
                                                💳 Make New Deposit
                                            </a>
                                        </td>
                                        <td style="padding: 0 10px;">
                                            <a href="#"
                                                style="display: inline-block; background: linear-gradient(135deg, #6c757d, #495057); color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 25px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3); transition: all 0.3s ease;">
                                                💬 Contact Support
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </td>
                    </tr>

                    <!-- Support Information -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; border-top: 1px solid #e9ecef;">
                            <h3
                                style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px; font-weight: 600; text-align: center;">
                                🤝 Need Help?</h3>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding: 10px;">
                                        <div style="color: #6c757d; font-size: 14px;">
                                            <strong>📧 Email:</strong>
                                            {{ config('mail.support_email', 'support@' . str_replace(['http://', 'https://'], '', config('app.url'))) }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; padding: 10px;">
                                        <div style="color: #6c757d; font-size: 14px;">
                                            <strong>📞 Phone:</strong>
                                            {{ config('app.support_phone', '+91-XXXXXXXXXX') }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; padding: 10px;">
                                        <a href="#"
                                            style="color: #007bff; text-decoration: none; font-size: 14px;">
                                            <strong>📚 Help Center</strong>
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="text-align: center; color: #6c757d; font-size: 14px; margin: 20px 0 0 0;">
                                We appreciate your understanding and look forward to serving you better.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #2c3e50; padding: 25px; text-align: center; border-radius: 0 0 12px 12px;">
                            <p style="color: #ffffff; margin: 0 0 10px 0; font-weight: 600; font-size: 16px;">
                                Best regards,<br>
                                <strong>{{ config('app.name') }} Finance Team</strong>
                            </p>

                            <p style="color: #bdc3c7; margin: 15px 0 0 0; font-size: 12px; line-height: 1.4;">
                                If you have questions about this transaction, please contact our support team with your
                                transaction ID:
                                <strong
                                    style="color: #ffffff;">{{ $transaction->id ?? $transaction->id }}</strong>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
