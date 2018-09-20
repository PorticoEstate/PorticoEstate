<div class="booking-cart" id="applications-cart-content" data-bind="visible: applicationCartItems().length > 0">
                <div class="booking-cart-title">
                    <span class="font-weight-bold">Søknader </span><span data-bind="text: '('+applicationCartItems().length+')'"></span>
                    
                    <i class="booking-cart-icon fas fa-plus float-right mr-2"></i>
                </div>
                <div class="booking-cart-items" data-bind="foreach: applicationCartItems" style="display: none;">
                    <div class="booking-cart-item">
                        <div class="row">                            
                            <div class="col-5" data-bind="text: building_name"></div>
                            <div class="col-6 d-inline" data-bind="foreach: resources"><span class="mr-3" data-bind="text: name"></span></div>
                            <div class="col-1 float-right"><span data-bind="click: $parent.deleteItem" class="far fa-trash-alt mr-2"></span></div>
                        </div>
                        <div class="row" data-bind="foreach: dates">
                            <div class="col-5" data-bind="text: date"></div>
                            <div class="col-6" data-bind="text: 'kl. ' + periode"></div>                        
                        </div>
                    </div>
                </div>
            </div> 
            
<footer class="footer">
    <div class="container">
    <div class="row">
        <div class="col-md-6">
            <h5 class="font-weight-bold">{site_title}</h5>
            <span><a href="#">Om tjeneste</a></span>
            <span><a href="{manual_url}">{manual_text}</a></span>
            <span><a href="#">Om Personvern</a></span>
        </div>
        <div class="col-md-6">
            <span class="font-weight-bold">Adresse</span>
            <span>Bergen kommune</span>
            <span>Olav Kyrres gate 19</span>
            <span>Postboks 8001</span>
            <span>4068 Bergen</span>
        </div>
    </div>
    </div>
</footer>
</div>
</body>
</html>
