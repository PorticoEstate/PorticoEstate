package no.bimconverter.ifc.v2x3.object.element;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.v2x3.object.FacilityManagementEntity;
import no.bimconverter.ifc.v2x3.object.element.type.FurnishingType;


import jsdai.SIfc2x3.EIfcbuildingstorey;
import jsdai.SIfc2x3.EIfcdoor;
import jsdai.SIfc2x3.EIfcelement;
import jsdai.SIfc2x3.EIfcfurnishingelement;
import jsdai.SIfc2x3.EIfcfurnituretype;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcspace;
import jsdai.SIfc2x3.EIfctypeobject;
import jsdai.SIfc2x3.EIfcwindowstyle;
import jsdai.lang.SdaiException;
@XmlRootElement
public class Furnishing  extends CommonElement implements FacilityManagementEntity{
	final static private Class<EIfcfurnishingelement> ifcEntityType = EIfcfurnishingelement.class;
	private FurnishingType furnishingType = null;
	
	public Furnishing() {
		super();
		
	}
	@Override
	public Class<? extends EIfcobjectdefinition> getIfcEntityType() {
		return ifcEntityType;
	}
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfcfurnishingelement element = (EIfcfurnishingelement)object;
		
		try {
			this.loadSpatialContainer(element);
			this.loadFurnishingType(element);
		} catch (SdaiException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	private void loadSpatialContainer(EIfcelement entity) throws SdaiException {
		
		this.loadParentItemsIntoSpatialContainer(entity, EIfcspace.class);
	}
	private void loadFurnishingType(EIfcfurnishingelement entity) throws SdaiException {
		EIfctypeobject typeObject = super.getTypeObject(entity);
		if(typeObject != null && typeObject.isKindOf(EIfcfurnituretype.class)) {
			this.furnishingType = new FurnishingType();
			this.furnishingType.load((EIfcfurnituretype) typeObject);
			
		}
	}
}
