<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body style="font: 400 13px Arial, sans-serif; color:#000000; padding:20px; line-height:1.8em;">
  <div>
    <strong>Thank you for your enquiry, {{ $data['name'] ?? '' }}!</strong><br>
    --------------------------------------------------------------------------------<br>
    We've received your {{ $data['enquiry_type'] ?? 'enquiry' }} and one of our travel experts will get back to you shortly.
    <br><br>

    @if(!empty($data['message']))
      <strong>Your message :-</strong><br>
      {{ $data['message'] }}<br><br>
    @endif

    @php
      $skipFields = ['name','email','phone','message','category','enquiry_type','current_url','ip'];
    @endphp

    @if(collect($data)->except($skipFields)->filter()->isNotEmpty())
      <strong>Your trip details :-</strong><br>
      @foreach($data as $key => $value)
        @if(!in_array($key, $skipFields) && !empty($value))
          {{ ucwords(str_replace('_',' ', $key)) }} :&nbsp;&nbsp;{{ $value }}<br>
        @endif
      @endforeach
      <br>
    @endif

    --------------------------------------------------------------------------------<br>
    REFERER : {{ $data['current_url'] ?? 'N/A' }}<br>
    @if(!empty($data['ip']))
      IP Address : {{ $data['ip'] }}<br>
    @endif
    --------------------------------------------------------------------------------<br>
    Regards,<br>
    Indian Panorama Team
  </div>
</body>
</html>
