<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body style="font: 400 13px Arial, sans-serif; color:#000000; padding:20px; line-height:1.8em;">
  <div>
    <strong>Travel Reservation Enquiry through www.indianpanorama.in</strong><br>
    --------------------------------------------------------------------------------<br>
    <strong>From :-</strong><br>

    @php
      $fromFields = ['name','email','phone','city','country','state'];
    @endphp

    @foreach($fromFields as $field)
      @if(!empty($data[$field]))
        &nbsp;&nbsp;{{ $data[$field] }}<br>
      @endif
    @endforeach

    @if(!empty($data['message']))
      --------------------------------------------------------------------------------<br>
      <strong>Travel Dreams :-</strong><br>
      {{ $data['message'] }}<br>
    @endif

    --------------------------------------------------------------------------------<br>

    @php
      $skipFields = array_merge($fromFields, ['date','message','ip','id','current_url','page','is_admin_view']);
    @endphp

    @foreach($data as $key => $value)
      @if(!in_array($key, $skipFields) && !empty($value))
        {{ ucwords(str_replace('_',' ', $key)) }} :&nbsp;&nbsp;{{ ucwords($value) }}<br>
      @endif
    @endforeach

    <br>--------------------------------------------------------------------------------<br>
    REFERER : {{ $data['current_url'] ?? 'N/A' }}<br>
    @if(!empty($data['ip']))
      IP Address : {{ $data['ip'] }}
    @endif
  </div>
</body>
</html>
