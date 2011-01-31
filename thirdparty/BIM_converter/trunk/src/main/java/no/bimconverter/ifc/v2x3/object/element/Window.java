package no.bimconverter.ifc.v2x3.object.element;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.v2x3.object.element.type.WindowStyle;

import jsdai.SIfc2x3.EIfcbuildingstorey;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfctypeobject;
import jsdai.SIfc2x3.EIfcwindow;
import jsdai.SIfc2x3.EIfcwindowstyle;
import jsdai.lang.SdaiException;

@XmlRootElement
public class Window extends CommonElement{
	final public static String commonPropertyName = "Pset_WindowCommon";
	private WindowStyle windowStyle = null;
	
	public Window() {
		super();
	}
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfcwindow entity = (EIfcwindow) object;
		try {
			this.loadSpatialContainer(entity);
			this.loadWindowStyle(entity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}

	private void loadSpatialContainer(EIfcwindow entity) throws SdaiException {
		this.loadParentItemsIntoSpatialContainer(entity, EIfcbuildingstorey.class);
		
	}
	
	private void loadWindowStyle(EIfcwindow entity) throws SdaiException {
		EIfctypeobject typeObject = super.getTypeObject(entity);
		if(typeObject != null && typeObject.isKindOf(EIfcwindowstyle.class)) {
			this.windowStyle = new WindowStyle();
			this.windowStyle.load((EIfcwindowstyle) typeObject);
			
		}
	}
	
	public WindowStyle getWindowStyle() {
		return windowStyle;
	}

	public void setWindowStyle(WindowStyle windowStyle) {
		this.windowStyle = windowStyle;
	}
}
