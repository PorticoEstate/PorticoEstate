package no.bimconverter.ifc.loader;

import java.util.List;

import no.bimconverter.ifc.v2x3.object.element.Window;

import jsdai.SIfc2x3.EIfcwindow;
import jsdai.lang.SdaiModel;


public class WindowLoader  extends CommonLoader {
	Class<EIfcwindow> windowClass = EIfcwindow.class;
	public WindowLoader() {
	}
	
	public List<Window> load(SdaiModel model) {
		return (List<Window>) super.load(model, EIfcwindow.class, Window.class);
	}
}