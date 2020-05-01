@extends('layouts.email')

@section('content')

    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Hi {{ $first_name }} {{ $last_name }},
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        We just got your order, but the cleaner declined your request for some reasons and your request
        will be re-assigned to another nearby WhavPro. 
        <br>
        We are committed to helping you have an hygenic and enviable environment. You can rest assured knowing that we provide the same quality service
        for you. 
        <br>
        For further inquiries, kindly call our helpline XXXX-XXX-XXX or send a mail to admin@whavit.com.

    </p>
    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
        <tbody>
        <tr>
            <td align="left" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                <tbody>
                {{-- <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #1181dd; border-radius: 5px; text-align: center;"> 
                        <a href="https://whavit.com/login" target="_blank" style="display: inline-block; color: #ffffff; background-color: #1181dd; border: solid 1px #1181dd; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #1181dd;">
                            Start Here
                        </a> 
                    </td>
                </tr> --}}
                </tbody>
            </table>
            </td>
        </tr>
        </tbody>
    </table>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Thank you for your understanding.
    </p>
@endsection
