@component('mail::message')
    # 🌟 Thank You for Your Submission! 🌟

    Dear **{{ $finalForm->name }}**,

    We have successfully received your registration form for **Christ Land City**. Your application is now being processed.

    ---

    ### 📋 Application Details

    | **Field** | **Details** |
    |:----------|:------------|
    | **Registration No** | `{{ $finalForm->registration_no ?? 'N/A' }}` |
    | **Name** | {{ $finalForm->name }} |
    | **CNIC** | {{ $finalForm->cnic }} |
    | **Submitted On** | {{ now()->format('d M, Y h:i A') }} |

    ---

    ### 📎 Your Form is Ready!

    Please click the button below to download your submitted form:

    @component('mail::button', ['url' => route('user.form.download', $finalForm->id), 'color' => 'success'])
        📥 **Download Your Form (PDF)**
    @endcomponent

    @component('mail::panel')
        ### 📌 What's Next?
        - Your application will be reviewed within **2-3 working days**
        - You will receive a confirmation email once verified
        - For any queries, please contact our support team
    @endcomponent

    @component('mail::table')
        | **Important Notes** |
        |:--------------------|
        | ✅ Keep this email for your records |
        | ✅ The attached PDF is your official receipt |
        | ✅ Contact us if you find any discrepancies |
    @endcomponent

    ---

    We appreciate your interest in **Christ Land City** and wish you the best in this journey!

    Warm Regards,
    **The Christ Land City Team**
    📍 Raja Javed Plaza, GT Road, Islamabad
    📞 +92 330 7778851 | +92 303 0366668
    ✉️ support@christlandcity.com

    ---

    @component('mail::subcopy')
        This is an automated message from {{ config('app.name') }}. Please do not reply to this email.
    @endcomponent

@endcomponent
