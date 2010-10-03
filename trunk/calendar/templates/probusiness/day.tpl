<!-- BEGIN day -->
<table class="basic" align="center">
	<tr>
		<td style="width: 100%; text-align: center" class="top">
			<span style="font-weight: bold">{date}</span>&nbsp;-&nbsp;{username}
		</td>
		<td rowspan="2">
			{small_calendar}
		</td>
	</tr>
	<tr>
		<td>
			<table class="basic">
				{day_events}
			</table>
		</td>
	</tr>
</table>
<br />
{print}
<!-- END day -->

<!-- BEGIN day_event -->
      <tr>
       <td class="right">
        {daily_events}
       </td>
      </tr>
<!-- END day_event -->

