@extends('layouts.email')

@section('content')

    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Hi {{ $first_name }} {{ $last_name }},
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        CONGRATULATIONS for beginning your application to becoming a WhavPro and getting to this stage.
        We are excited to learn more about you. <br><br>
        We usually have our in-house interview and training on Wednesdays by 10am. Kindly visit our office
        at 9a, Abagbon Close, Off Adeola Odeku, Victoria Island, Lagos, for the next stage of selection. <br><br>
        Also, come along with the following; <br>
        - A valid means of identification <br>
        - 2 reference letters <br>
        - Dress-code- Business Casual
        
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
        See you!
    </p>
@endsection
