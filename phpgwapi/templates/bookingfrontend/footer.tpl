</div>
<div id="footer">

</div>
<div class="footer l-box is-center">
	{footer_address}
</div>
</body>
{javascript_end}
<script>
	(function (window, document)
	{
		document.getElementById('toggle').addEventListener('click', function (e)
		{
			document.getElementById('tuckedMenu').classList.toggle('custom-menu-tucked');
			document.getElementById('toggle').classList.toggle('x');
		});
	})(this, this.document);
</script>
</html>
