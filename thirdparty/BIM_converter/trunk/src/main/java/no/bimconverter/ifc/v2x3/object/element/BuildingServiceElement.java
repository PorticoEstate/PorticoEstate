package no.bimconverter.ifc.v2x3.object.element;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.v2x3.object.FacilityManagementEntity;
import no.bimconverter.ifc.v2x3.object.element.type.BuildingServiceElementType;
import no.bimconverter.ifc.v2x3.object.element.type.WindowStyle;

import jsdai.SIfc2x3.EIfcbuildingelementtype;
import jsdai.SIfc2x3.EIfcbuildingstorey;
import jsdai.SIfc2x3.EIfcdistributionelement;
import jsdai.SIfc2x3.EIfcdistributionelementtype;
import jsdai.SIfc2x3.EIfcfurnishingelement;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfctypeobject;
import jsdai.SIfc2x3.EIfcwindow;
import jsdai.SIfc2x3.EIfcwindowstyle;
import jsdai.lang.SdaiException;

@XmlRootElement
public class BuildingServiceElement extends CommonElement implements FacilityManagementEntity{
	final static private Class<EIfcdistributionelement> ifcEntityType = EIfcdistributionelement.class;
	private BuildingServiceElementType buildingServiceElemenType = null;
	
	
	public BuildingServiceElement() {
		super();
	}
	@Override
	public Class<? extends EIfcobjectdefinition> getIfcEntityType() {
		return ifcEntityType;
	}
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfcdistributionelement entity = (EIfcdistributionelement) object;
		try {
			this.loadSpatialContainer(entity);
			this.loadBuildingServiceElementType(entity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}

	private void loadSpatialContainer(EIfcdistributionelement entity) throws SdaiException {
		this.loadParentItemsIntoSpatialContainer(entity, EIfcbuildingstorey.class);
		
	}
	
	private void loadBuildingServiceElementType(EIfcdistributionelement entity) throws SdaiException {
		EIfctypeobject typeObject = super.getTypeObject(entity);
		if(typeObject != null && typeObject.isKindOf(EIfcdistributionelementtype.class)) {
			this.buildingServiceElemenType = new BuildingServiceElementType();
			this.buildingServiceElemenType.load((EIfcdistributionelementtype) typeObject);
		}
	}
	public BuildingServiceElementType getBuildingServiceElemenType() {
		return buildingServiceElemenType;
	}
	public void setBuildingServiceElemenType(
			BuildingServiceElementType buildingServiceElemenType) {
		this.buildingServiceElemenType = buildingServiceElemenType;
	}
	
	
}
