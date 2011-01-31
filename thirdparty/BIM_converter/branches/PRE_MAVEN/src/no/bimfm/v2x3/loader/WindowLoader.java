package no.bimfm.v2x3.loader;

import java.util.List;

import jsdai.SIfc2x3.EIfcwindow;
import jsdai.lang.SdaiModel;
import no.bimfm.ifc.v2x3.object.element.Window;

public class WindowLoader  extends CommonLoader {
	Class<EIfcwindow> windowClass = EIfcwindow.class;
	public WindowLoader() {
	}
	
	public List<Window> load(SdaiModel model) {
		return (List<Window>) super.load(model, EIfcwindow.class, Window.class);
	}
}