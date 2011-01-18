package no.bimfm.ifc.v2x3.object;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.annotation.XmlRootElement;



import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcpostaladdress;
import jsdai.SIfc2x3.EIfcroot;
import jsdai.SIfc2x3.EIfcsite;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
import no.bimfm.ifc.IfcSdaiException;

@XmlRootElement
public class Site extends SpatialStructure{
	final private static String commonPropertyName = "Pset_SiteCommon";

	
	
	
	
	private  no.bimfm.ifc.v2x3.owner.Address address;
	public  no.bimfm.ifc.v2x3.owner.Address getAddress() {
		return address;
	}

	public void setAddress( no.bimfm.ifc.v2x3.owner.Address address) {
		this.address = address;
	}
	Map<String, String> classification = new HashMap<String, String>();
	SdaiIterator integerIterator;
	
	public Site() {
		super();
	}
		
	private void loadAttributes(EIfcsite site) throws SdaiException {
		
		if(site.testReflatitude(null)) {
			//this.attributes.put(ATTRIBUTE_KEY_LATITUDE, this.getRefLatitude(site));
			this.attributes.setLatitude(this.getRefLatitude(site));
		}
		if(site.testReflongitude(null)) {
			//this.attributes.put(ATTRIBUTE_KEY_LONGTITUDE, this.getRefLongtitude(site));
			this.attributes.setLongtitude(this.getRefLongtitude(site));
		}
		if(site.testRefelevation(null)) {
			//this.attributes.put(ATTRIBUTE_KEY_ELEVATION, String.valueOf(site.getRefelevation(null)));
			this.attributes.setElevation( String.valueOf(site.getRefelevation(null)));
		}
	}
	
	private String getRefLatitude(EIfcsite site) throws SdaiException {
		return this.getIntListAsString(site.getReflatitude(null));
	}
	private String getRefLongtitude(EIfcsite site) throws SdaiException {
		return this.getIntListAsString(site.getReflongitude(null));
	}
	
	/*
	 * Note, site HAS to have an address if the building does not have an address
	 * TODO: link site to building with relation to the address, i.e. check that
	 * the address is available either on site or building
	 */
	private void loadAddress(EIfcsite site) throws SdaiException {
		if(site.testSiteaddress(null)) {
			EIfcpostaladdress siteAddress = site.getSiteaddress(null);
			//this.address = super.loadAddress(siteAddress);
			this.address = new no.bimfm.ifc.v2x3.owner.Address().load(siteAddress);
			//System.out.println(this.address2);
		}
	}
	
	private void loadSiteProperties(EIfcsite site) throws SdaiException {
		if(this.propertiesList == null) {
			this.relateObjectPropertiesAndQuantities(site);
		}
		super.loadProperties(site);
		super.setCommonProperty(Site.commonPropertyName);
	}
	
	private void loadSpatialDecomposition(EIfcsite site) throws SdaiException {
		List<EIfcobjectdefinition> siteIsDecomposedBy = this.getIsDecomposedBy(site);
		
		super.insertDecomposingIds(siteIsDecomposedBy, Site.SpatialDecomposition.BUILDING.key);
		insertParentIds(site);
	}
	
	
	private void insertParentIds(EIfcsite site) throws SdaiException {
		List<EEntity> parents = this.getParentEntities(site);
		if( parents.size() == 1) {
				//this.spatialDecomposition.put(SpatialDecomposition.PROJECT.key, new String[]{((EIfcroot) parents.get(0)).getGlobalid(null)});
				this.spatialDecomposition.setProject(((EIfcroot) parents.get(0)).getGlobalid(null));
		} else {
			throw new IfcSdaiException("Error with parent element structure");
		}
	}
	

	
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfcsite siteEntity = (EIfcsite) object;
		try {
			
			this.loadAttributes(siteEntity);
			this.loadAddress(siteEntity);
			this.loadClassification(siteEntity);
			this.loadSiteProperties(siteEntity);
			this.loadSpatialDecomposition(siteEntity);
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new IfcSdaiException("Error loading site");
		}
		
	}

	
}
