package no.bimconverter.ifc.v2x3.object;


import java.util.List;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.IfcSdaiException;


import jsdai.SIfc2x3.EIfcbuildingstorey;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcroot;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;

@XmlRootElement
public class BuildingStorey  extends SpatialStructure {
	final private static String commonPropertyName = "Pset_BuildingStoreyCommon";
	public BuildingStorey() {
		super();
		
	}
	
	public void load(EIfcbuildingstorey buildingStoreyEntity) {
		super.load(buildingStoreyEntity);
		try {
			this.loadAttributes(buildingStoreyEntity);
			this.loadBaseQuantities(buildingStoreyEntity);
			this.loadClassification(buildingStoreyEntity);
			this.loadBuildingStoreyProperties(buildingStoreyEntity);
			this.loadSpatialDecomposition(buildingStoreyEntity);
			//TODO: spatial container
		} catch (SdaiException e) {
			e.printStackTrace();
		}
		
	}
	
	private void loadAttributes(EIfcbuildingstorey entity) throws SdaiException {
		
		if(entity.testElevation(null)) {
			this.attributes.setElevation( String.valueOf(entity.getElevation(null)));//put(Attribute.ELEVATION.key, String.valueOf(entity.getElevation(null)));
		}
	}
	
	private void loadBuildingStoreyProperties(EIfcbuildingstorey buildingStorey) throws SdaiException {
		if(this.propertiesList == null) {
			this.relateObjectPropertiesAndQuantities(buildingStorey);
		}
		super.loadProperties(buildingStorey);
		super.setCommonProperty(BuildingStorey.commonPropertyName);
	}
	private void loadSpatialDecomposition(EIfcbuildingstorey buildingStorey) throws SdaiException {
		List<EIfcobjectdefinition> siteIsDecomposedBy = this.getIsDecomposedBy(buildingStorey);
		super.insertDecomposingIds(siteIsDecomposedBy, BuildingStorey.SpatialDecomposition.SPACE.key);
		insertParentIds(buildingStorey);
		
	}
	
	public void insertParentIds(EIfcbuildingstorey buildingStorey) throws SdaiException {
		List<EEntity> parents = this.getParentEntities(buildingStorey);
		if( parents.size() == 3 || parents.size() == 2) {
			this.spatialDecomposition.addBuildingId(((EIfcroot) parents.get(0)).getGlobalid(null)); //put(SpatialDecomposition.BUILDING.key, new String[]{((EIfcroot) parents.get(0)).getGlobalid(null)});
			if(parents.size() == 2) {
				this.spatialDecomposition.setProject(((EIfcroot) parents.get(1)).getGlobalid(null));//put(SpatialDecomposition.PROJECT.key, new String[]{((EIfcroot) parents.get(1)).getGlobalid(null)});
			} else {
				this.spatialDecomposition.setSite(((EIfcroot) parents.get(1)).getGlobalid(null));//put(SpatialDecomposition.SITE.key, new String[]{((EIfcroot) parents.get(1)).getGlobalid(null)});
				this.spatialDecomposition.setProject(((EIfcroot) parents.get(2)).getGlobalid(null));//put(SpatialDecomposition.PROJECT.key, new String[]{((EIfcroot) parents.get(2)).getGlobalid(null)});
			}
		} else {
			throw new IfcSdaiException("Error with parent element structure");
		}
	}
	
	
	

	
	
	public enum Attribute {
		ELEVATION("elevation");
		private final String key;
		Attribute(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}
	
	

}
