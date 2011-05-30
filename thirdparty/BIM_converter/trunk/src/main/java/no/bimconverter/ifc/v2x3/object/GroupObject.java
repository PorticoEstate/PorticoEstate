package no.bimconverter.ifc.v2x3.object;

import java.util.ArrayList;
import java.util.List;

import jsdai.SIfc2x3.AIfcobjectdefinition;
import jsdai.SIfc2x3.AIfcrelassignstogroup;
import jsdai.SIfc2x3.EIfcgroup;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcrelassignstogroup;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;

/*
 * Analogous to EIfcGroup
 * Parent class to System, Zone, etc.
 */
public class GroupObject extends CommonObjectImpl{
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
