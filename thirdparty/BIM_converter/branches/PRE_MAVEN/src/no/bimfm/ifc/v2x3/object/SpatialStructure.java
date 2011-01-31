package no.bimfm.ifc.v2x3.object;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import no.bimfm.jaxb.Decomposition;

import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcpostaladdress;
import jsdai.SIfc2x3.EIfcspatialstructureelement;
import jsdai.lang.SdaiException;

public class SpatialStructure extends CommonObjectImpl {
	//protected Map<String, String[]> spatialDecomposition = new HashMap<String, String[]>();
	protected Decomposition spatialDecomposition = new Decomposition();
	public SpatialStructure() {
		super();
	}
	
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		
		try {
			
			this.loadAttributes((EIfcspatialstructureelement)object);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	protected void loadAttributes(EIfcspatialstructureelement entity) throws SdaiException {
		
		
		if(entity.testLongname(null)) {
			this.attributes.setLongName(entity.getLongname(null));
			
		}
	}
	
	/*
	public Map<String, String> loadAddress(EIfcpostaladdress postalAddress) throws SdaiException{
		Map<String, String> addressMap = new HashMap<String, String>();
		if(postalAddress.testAddresslines(null)) {
			String address = super.getStringListAsString(postalAddress.getAddresslines(null));
			addressMap.put(Address.ADDRESS.key, address);
		}
		if(postalAddress.testTown(null)) {
			addressMap.put(Address.CITY.key, postalAddress.getTown(null));
		}
		if(postalAddress.testRegion(null)) {
			addressMap.put(Address.REGION.key, postalAddress.getRegion(null));
		}
		if(postalAddress.testPostalcode(null)) {
			addressMap.put(Address.POSTALCODE.key, postalAddress.getPostalcode(null));
		}
		return addressMap;
	}
	*/

	public enum Attribute {
		LONG_NAME("Long name");
		
		private final String key;
		Attribute(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}
	
	protected void insertDecomposingIds(List<EIfcobjectdefinition> siteIsDecomposedBy, String key) throws SdaiException {
		if(siteIsDecomposedBy.size() > 0) {
			//String[] buildingGuids = new String[siteIsDecomposedBy.size()];
			for(int i = 0; i < siteIsDecomposedBy.size(); i++) {
				//buildingGuids[i] = siteIsDecomposedBy.get(i).getGlobalid(null);
				if(key.equals(Building.SpatialDecomposition.STOREY.key)) {
					this.spatialDecomposition.addBuildingStoreyId(siteIsDecomposedBy.get(i).getGlobalid(null));
				} else if (key.equals(Site.SpatialDecomposition.BUILDING.key)) {
					this.spatialDecomposition.addBuildingId(siteIsDecomposedBy.get(i).getGlobalid(null));
				} else if (key.equals(BuildingStorey.SpatialDecomposition.SPACE.key)) {
					this.spatialDecomposition.addSpaceId(siteIsDecomposedBy.get(i).getGlobalid(null));
				}
				
			}
			//this.spatialDecomposition.put(key, buildingGuids);
		} 
		/*
		else {
			throw new IfcSdaiException("Error! No decomposing items found!");
		}
		*/
	}
	/*
	public Map<String, String[]> getSpatialDecomposition() {
		return spatialDecomposition;
	}

	public void setSpatialDecomposition(Map<String, String[]> spatialDecomposition) {
		this.spatialDecomposition = spatialDecomposition;
	}
	*/
	public enum SpatialDecomposition {
		PROJECT("Project"),
		SITE("Site"),
		BUILDING("Building"),
		STOREY("Storey"),
		SPACE("Space");
		final String key;
		SpatialDecomposition(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}
	public Decomposition getSpatialDecomposition() {
		return spatialDecomposition;
	}

	public void setSpatialDecomposition(Decomposition spatialDecomposition) {
		this.spatialDecomposition = spatialDecomposition;
	}
	
}
