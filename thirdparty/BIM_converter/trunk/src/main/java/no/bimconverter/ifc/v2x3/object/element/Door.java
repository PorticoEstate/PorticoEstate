package no.bimconverter.ifc.v2x3.object.element;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.v2x3.object.FacilityManagementEntity;
import no.bimconverter.ifc.v2x3.object.element.type.DoorStyle;


import jsdai.SIfc2x3.EIfcdoor;
import jsdai.SIfc2x3.EIfcdoorstyle;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcspace;
import jsdai.SIfc2x3.EIfctypeobject;
import jsdai.lang.SdaiException;
@XmlRootElement
public class Door extends CommonElement implements FacilityManagementEntity{
	final static private Class<EIfcdoor> ifcEntityType = EIfcdoor.class;
	private DoorStyle doorStyle = null;
	
	public DoorStyle getDoorStyle() {
		return doorStyle;
	}
	@Override
	public Class<? extends EIfcobjectdefinition> getIfcEntityType() {
		return ifcEntityType;
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
