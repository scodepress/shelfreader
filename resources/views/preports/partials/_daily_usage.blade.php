<h2>Daily Usage</h2>
<table width="100%">
  <tr style="font-size: 1.3em;font-weight: bold;">
 
    <td>Date</td>
    <td>Scans</td>
    <td>Corrections</td>
    <td>Alerts</td>
    <td>Not Found</td>
  </tr>
@foreach($usage as $key=>$k)
<tr><td>{{$k->date}}</td><td>{{$k->si}}</td>
<td>
  {{ \App\Preport::dailyErrors($k->date) }}
</td>
<td>
{{ \App\Preport::statusAlerts($k->date) }}
</td>
<td>
{{ \App\Preport::Shadowed($k->date) }}
</td>
</tr>
@endforeach
</table>