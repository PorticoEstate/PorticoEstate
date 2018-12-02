<div class="booking-cart" id="applications-cart-content" data-bind="visible: applicationCartItems().length > 0 && visible">
                <div class="booking-cart-title">
                    <span class="font-weight-bold">{cart_header} </span><span data-bind="text: '('+applicationCartItems().length+')'"></span>
                    
                    <i class="booking-cart-icon fas fa-plus float-right mr-2"></i>
                </div>
                <div class="booking-cart-items" style="display: none;">
                    <div data-bind="foreach: applicationCartItems">
                        <div class="booking-cart-item">
                            <div class="row">                            
                                <div class="col-5" data-bind="text: building_name"></div>
                                <div class="col-5 d-inline"><span class="mr-3" data-bind="text: joinedResources"></span></div>
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
            
<footer class="footer">
    <div class="container">
    <div class="row">
        <div class="col-md-6">
            <h5 class="font-weight-bold">{site_title}</h5>
            <span><a href="https://www.aktiv-kommune.no/" target="_blank">{footer_about}</a></span>
            <span><a href="{footer_privacy_link}" target="_blank">{footer_privacy_title}</a></span>
        </div>
    </div>
    </div>
</footer>
</div>
		<script type="text/javascript">
			var footerlang = {"Do you want to delete application?": "{cart_confirm_delete}"};
		</script>
</body>
</html>
