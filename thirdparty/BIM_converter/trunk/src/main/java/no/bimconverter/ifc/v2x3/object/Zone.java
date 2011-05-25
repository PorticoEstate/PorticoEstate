package no.bimconverter.ifc.v2x3.object;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.jaxb.ZoneAssignment;

import jsdai.SIfc2x3.AIfcobjectdefinition;
import jsdai.SIfc2x3.AIfcrelassignstogroup;
import jsdai.SIfc2x3.EIfcgroup;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcrelassignstogroup;
import jsdai.SIfc2x3.EIfcspace;
import jsdai.SIfc2x3.EIfcsystem;
import jsdai.SIfc2x3.EIfczone;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
@XmlRootElement
public class Zone extends CommonObjectImpl implements FacilityManagementEntity{
	final static private Class<EIfczone> ifcEntityType = EIfczone.class;
	private ZoneAssignment zoneAssignment = new ZoneAssignment();
	public Zone() {
	}
	@Override
	public Class<? extends EIfcobjectdefinition> getIfcEntityType() {
		return ifcEntityType;
	}
	
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfczone entity = (EIfczone)object;
		try {
			this.loadAttributes(entity);
			this.loadClassification(entity);
			this.loadProperties(entity);
			this.loadAssignment(entity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}

	private void loadAssignment(EIfczone entity) throws SdaiException {
		// TODO Auto-generated method stub
		AIfcrelassignstogroup groupAgg = entity.getIsgroupedby(null, null);
		SdaiIterator groupIterator = groupAgg.createIterator();
		while(groupIterator.next()) {
			EIfcrelassignstogroup now = groupAgg.getCurrentMember(groupIterator);
			EIfcgroup group = now.getRelatinggroup(null);
			if(group.isKindOf(EIfczone.class)) {
				// Spaces
				AIfcobjectdefinition relatedObjects = now.getRelatedobjects(null);
				SdaiIterator objectIterator = relatedObjects.createIterator();
				while(objectIterator.next()) {
					EIfcobjectdefinition objDef = relatedObjects.getCurrentMember(objectIterator);
					if(objDef.isKindOf(EIfcspace.class)) {
						// add the guid of the space
						this.zoneAssignment.addSpaceId(objDef.getGlobalid(null));
					}
				}
				
			} else if(group.isKindOf(EIfcsystem.class)) {
				// Sub zones
				AIfcobjectdefinition relatedObjects = now.getRelatedobjects(null);
				SdaiIterator objectIterator = relatedObjects.createIterator();
				while(objectIterator.next()) {
					EIfcobjectdefinition objDef = relatedObjects.getCurrentMember(objectIterator);
					if(objDef.isKindOf(EIfczone.class)) {
						// add the guid of the zone
						this.zoneAssignment.addZoneId(objDef.getGlobalid(now));
					}
				}
			}
		}
	}

	public ZoneAssignment getZoneAssignment() {
		return zoneAssignment;
	}

	public void setZoneAssignment(ZoneAssignment zoneAssignment) {
		this.zoneAssignment = zoneAssignment;
	}

}
