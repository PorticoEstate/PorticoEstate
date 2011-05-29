package no.bimconverter.ifc.v2x3.object;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.AIfcobjectdefinition;
import jsdai.SIfc2x3.AIfcrelassignstogroup;
import jsdai.SIfc2x3.EIfcgroup;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcrelassignstogroup;
import jsdai.SIfc2x3.EIfcspace;
import jsdai.SIfc2x3.EIfcsystem;
import jsdai.SIfc2x3.EIfczone;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
@XmlRootElement
public class SystemObject extends CommonObjectImpl implements FacilityManagementEntity{
	final static private Class<EIfcsystem> ifcEntityType = EIfcsystem.class;
	
	public SystemObject() {
	}
	
	@Override
	public Class<? extends EIfcobjectdefinition> getIfcEntityType() {
		return ifcEntityType;
	}
	
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfcsystem entity = (EIfcsystem)object;
		try {
			this.loadClassification(entity);
			this.loadProperties(entity);
			
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	private void loadAssignment(EIfczone entity) throws SdaiException {
		List<String> spaceIds = this.loadAssignments(entity, EIfczone.class, EIfcspace.class);
		for ( String spaceId : spaceIds) {
			this.zoneAssignment.addSpaceId(spaceId);
		}
		List<String> zoneIds = this.loadAssignments(entity, EIfcsystem.class, EIfczone.class);
		for ( String spaceId : zoneIds) {
			this.zoneAssignment.addZoneId(spaceId);
		}
		
	}
	
	protected List<String> loadAssignments(EIfcgroup entity, Class<? extends EEntity> relatingGroupClass, Class<? extends EEntity> relatedObjectClass) throws SdaiException {
		List<String> guidList = new ArrayList<String>();
		AIfcrelassignstogroup groupAgg = entity.getIsgroupedby(null, null);
		SdaiIterator groupIterator = groupAgg.createIterator();
		while(groupIterator.next()) {
			EIfcrelassignstogroup now = groupAgg.getCurrentMember(groupIterator);
			EIfcgroup group = now.getRelatinggroup(null);
			if(group.isKindOf(relatingGroupClass)) {
				AIfcobjectdefinition relatedObjects = now.getRelatedobjects(null);
				SdaiIterator objectIterator = relatedObjects.createIterator();
				while(objectIterator.next()) {
					EIfcobjectdefinition objDef = relatedObjects.getCurrentMember(objectIterator);
					if(objDef.isKindOf(relatedObjectClass)) {
						guidList.add(objDef.getGlobalid(null));
					}
				}
			}
		}
		return guidList;
	}
}
