package no.bimconverter.ifc.v2x3.object;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.IfcSdaiException;
import no.bimconverter.ifc.jaxb.owner.Address;


import jsdai.SIfc2x3.EIfcbuilding;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcpostaladdress;
import jsdai.SIfc2x3.EIfcroot;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;

@XmlRootElement
public class Building extends SpatialStructure {
	final private static String commonPropertyName = "Pset_BuildingCommon";
	private Address address;
	
	public Building() {
		super();
		
	}
	
	public void load(EIfcbuilding buildingEntity) {
		super.load(buildingEntity);
		try {
			this.loadAttributes(buildingEntity);
			this.loadAddress(buildingEntity);
			this.loadClassification(buildingEntity);
			this.loadBaseQuantities(buildingEntity);
			this.loadBuildingProperties(buildingEntity);
			this.loadSpatialDecomposition(buildingEntity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	private void loadSpatialDecomposition(EIfcbuilding building) throws SdaiException {
		
		
		List<EIfcobjectdefinition> siteIsDecomposedBy = this.getIsDecomposedBy(building);
		super.insertDecomposingIds(siteIsDecomposedBy, Building.SpatialDecomposition.STOREY.key);
		
		this.insertParentIds(building);
		
	}
	
	
	private void insertParentIds(EIfcbuilding building) throws SdaiException {
		List<EEntity> parents = this.getParentEntities(building);
		if( parents.size() == 2 || parents.size() == 1) {
			if(parents.size() == 2) {
				//this.spatialDecomposition.put(SpatialDecomposition.SITE.key, new String[]{((EIfcroot) parents.get(0)).getGlobalid(null)});
				//this.spatialDecomposition.put(SpatialDecomposition.PROJECT.key, new String[]{((EIfcroot) parents.get(1)).getGlobalid(null)});
				this.spatialDecomposition.setSite(((EIfcroot) parents.get(0)).getGlobalid(null));
				this.spatialDecomposition.setProject(((EIfcroot) parents.get(1)).getGlobalid(null));
				
			} else {
				//this.spatialDecomposition.put(SpatialDecomposition.SITE.key, null);
				//this.spatialDecomposition.put(SpatialDecomposition.PROJECT.key, new String[]{((EIfcroot) parents.get(0)).getGlobalid(null)});
				this.spatialDecomposition.setProject(((EIfcroot) parents.get(0)).getGlobalid(null));
			}
		} else {
			throw new IfcSdaiException("Error with parent element structure");
		}
	}
	

	
	private void loadBuildingProperties(EIfcbuilding building) throws SdaiException {
		if(this.propertiesList == null) {
			this.relateObjectPropertiesAndQuantities(building);
		}
		super.loadProperties(building);
		super.setCommonProperty(Building.commonPropertyName);
	}
	
	private void loadBaseQuantities(EIfcbuilding buildingEntity) {
		// TODO: Finish this method!
		
	}

	private void loadAddress(EIfcbuilding building) throws SdaiException {
		if(building.testBuildingaddress(null)) {
			//this.address = super.initializeAddress();
			EIfcpostaladdress postalAddress = building.getBuildingaddress(null);
			//this.address = super.loadAddress(postalAddress);
			this.address = new Address().load(postalAddress);
		}
	}
	
	
	public void setAddress(Address address) {
		this.address = address;
	}

	public Address getAddress() {
		return this.address;
		
	}
	
	
}