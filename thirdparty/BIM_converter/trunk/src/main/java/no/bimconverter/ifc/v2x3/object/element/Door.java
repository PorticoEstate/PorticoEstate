package no.bimconverter.ifc.v2x3.object.element;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.v2x3.object.element.type.DoorStyle;


import jsdai.SIfc2x3.EIfcbuildingstorey;
import jsdai.SIfc2x3.EIfcdoor;
import jsdai.SIfc2x3.EIfcdoorstyle;
import jsdai.SIfc2x3.EIfcobject;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcspace;
import jsdai.SIfc2x3.EIfctypeobject;
import jsdai.SIfc2x3.EIfcwindowstyle;
import jsdai.lang.SdaiException;
@XmlRootElement
public class Door extends CommonElement{
	private DoorStyle doorStyle = null;
	
	public DoorStyle getDoorStyle() {
		return doorStyle;
	}

	public void setDoorStyle(DoorStyle doorStyle) {
		this.doorStyle = doorStyle;
	}

	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfcdoor entity = (EIfcdoor)object;
		try {
			this.loadSpatialContainer(entity);
			this.loadDoorStyle(entity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	private void loadSpatialContainer(EIfcdoor entity) throws SdaiException {
		//this.loadParentItemsIntoSpatialContainer(entity, EIfcbuildingstorey.class);
		this.loadParentItemsIntoSpatialContainer(entity, EIfcspace.class);
	}
	
	private void loadDoorStyle(EIfcdoor entity) throws SdaiException {
		EIfctypeobject typeObject = super.getTypeObject(entity);
		if(typeObject.isKindOf(EIfcdoorstyle.class)) {
			this.doorStyle = new DoorStyle();
			this.doorStyle.load((EIfcdoorstyle) typeObject);
			
		}
	}

}
