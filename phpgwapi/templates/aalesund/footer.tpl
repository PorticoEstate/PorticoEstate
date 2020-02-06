<div class="booking-cart" id="applications-cart-content" data-bind="visible: applicationCartItems().length > 0 && visible">
	<div class="booking-cart-title">
		<span class="font-weight-bold">{cart_header} </span><span data-bind="text: '('+applicationCartItems().length+')'"></span>
		<i class="booking-cart-icon fas fa-plus float-right mr-2"></i>
	</div>
	<div class="booking-cart-items">
		<div data-bind="foreach: applicationCartItems">
			<div class="booking-cart-item">
				<div class="row">
					<div class="col-5" data-bind="text: building_name"></div>
					<div class="col-5 d-inline" data-bind="foreach: resources"><span class="mr-3" data-bind="text: name"></span></div>
					<div class="col-2 float-right"><span data-bind="click: $parent.deleteItem" class="far fa-trash-alt mr-2"></span></div>
				</div>
				<div class="row" data-bind="foreach: dates">
					<div class="col-5" data-bind="text: date"></div>
					<div class="col-6" data-bind="text: 'kl. ' + periode"></div>
				</div>
			</div>
		</div>
		<div class="m-2">
			<button onclick="window.location.href = phpGWLink('bookingfrontend/', {menuaction:'bookingfrontend.uiapplication.add_contact' }, false)" class="btn btn-light m-2">
				{cart_complete_application}
			</button>
		</div>
	</div>
</div>
<footer class="footer"   >
    <div class="container"      id="nun">
		<div class="row">
			<div class="col-md-6 order-md-1 col-lg-3 order-lg-1">
                <span><a target="_blank" rel="noopener noreferrer" href="{footer_logo_url}"><img class="footer-brand footer-brand-site-img" src="{footer_logo_img}" alt="{logo_title}"/></a></span>
			</div>
			<div class="col-md-6 order-md-3 col-lg-3 order-lg-2"     id="logowidth">
				<h3>Kontakt</h3>
				<span><a target="_blank" rel="noopener noreferrer"  href="{footer_logo_url}">{municipality}</a></span>
				<span><a target="_blank" rel="noopener noreferrer"  href="mailto:{support_email}">{support_email}</a><br> </span>
			</div>
			<div class="col-md-6 order-md-2 col-lg-3 order-lg-3">
				<h3>Aktiv kommune</h3>
				<span><a   target="_blank" rel="noopener noreferrer"   href="https://www.aktiv-kommune.no/">{textaboutmunicipality}</a></span>
				<span><a   target="_blank" rel="noopener noreferrer"    href="https://www.aktiv-kommune.no/manual/">{manual}</a></span>
                <span><a   target="_blank" rel="noopener noreferrer"    href="{footer_privacy_link}">{privacy}</a></span>
			</div>
			<div class="col-md-6 order-md-4 col-lg-3 order-lg-4">
				<h3>Logg inn</h3>
				{user_info_view}
				<span><img class="login-logo" src="{loginlogo}" alt="Logg inn"></img><a href="{login_url}">{login_text}</a></span>
				{org_info_view}
				<span><img class="login-logo" src="{loginlogo}" alt="Logg inn"></img><a target="_blank" rel="noopener noreferrer" href="{executiveofficer_url}">{executiveofficer}</a></span>
			</div>
		</div>
    </div>
</footer>
</div>
<script>
	var footerlang = {"Do you want to delete application?": "{cart_confirm_delete}"};
</script>
	{javascript_end}
</body>
</html>
