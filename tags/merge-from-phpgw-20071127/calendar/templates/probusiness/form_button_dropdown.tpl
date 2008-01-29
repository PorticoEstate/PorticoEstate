  <td style="vertical-align: bottom">
    <form action="{form_link}" method="post" name="{form_name}form">
      <span style="font-weight: bold">{title}:</span><br />
      {hidden_vars}
      <select style="max-width: 400px; min-width: 90px" name="{form_name}" onchange="document.{form_name}form.submit()">
       {form_options}    
      </select>
      <noscript>
      	<input type="submit" value="{button_value}" />
      </noscript>
    </form>
  </td>
