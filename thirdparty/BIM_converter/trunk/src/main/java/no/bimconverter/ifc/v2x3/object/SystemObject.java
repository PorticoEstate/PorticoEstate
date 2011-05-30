package no.bimconverter.ifc.v2x3.object;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.jaxb.ComponentSystemAssignment;
import no.bimconverter.ifc.jaxb.SpatialContainerItem;

import jsdai.SIfc2x3.AIfcrelservicesbuildings;
import jsdai.SIfc2x3.AIfcspatialstructureelement;
import jsdai.SIfc2x3.EIfcdistributionelement;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcrelservicesbuildings;
import jsdai.SIfc2x3.EIfcspatialstructureelement;
import jsdai.SIfc2x3.EIfcsystem;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
@XmlRootElement(name="system")
public class SystemObject extends GroupObject implements FacilityManagementEntity{
	final static private Class<EIfcsystem> ifcEntityType = EIfcsystem.class;
	private ComponentSystemAssignment componentSystemAssignment = new ComponentSystemAssignment();
	private List<SpatialContainerItem> servicesBuildings;
	
	

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
			this.loadAssignment(entity);
			this.loadServicesBuildings(entity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	private void loadAssignment(EIfcsystem entity) throws SdaiException {
		List<String> componentIds = this.loadAssignments(entity, EIfcsystem.class, EIfcdistributionelement.class);
		for ( String componentId : componentIds) {
			this.componentSystemAssignment.addComponentGuid(componentId);
		}
		List<String> subSystemIds = this.loadAssignments(entity, EIfcsystem.class, EIfcsystem.class);
		for ( String subSystemId : subSystemIds) {
			this.componentSystemAssignment.addSubSystemId(subSystemId);
		}
		
	}
	
	private void loadServicesBuildings(EIfcsystem entity) throws SdaiException {
		AIfcrelservicesbuildings serviceBuildingsAggregate = entity.getServicesbuildings(null, null);
		SdaiIterator serviceBuildingsIterator = serviceBuildingsAggregate.createIterator();
		while(serviceBuildingsIterator.next()) {
			EIfcrelservicesbuildings servicesBuildings = serviceBuildingsAggregate.getCurrentMember(serviceBuildingsIterator);
			AIfcspatialstructureelement spatialStructureAggregation = servicesBuildings.getRelatedbuildings(null);
			SdaiIterator spatialStructureIterator = spatialStructureAggregation.createIterator();
			while(spatialStructureIterator.next()) {
				EIfcspatialstructureelement spatialStructure = spatialStructureAggregation.getCurrentMember(spatialStructureIterator);
				if(this.servicesBuildings == null) {
					this.servicesBuildings = new ArrayList<SpatialContainerItem>();
				}
				this.servicesBuildings.add(new SpatialContainerItem(spatialStructure.getGlobalid(null), spatialStructure.getInstanceType().getName(null), null, null));
			}
		}
	}
	 
	@XmlElement(name="component-system-assignment")
	public ComponentSystemAssignment getComponentSystemAssignment() {
		return componentSystemAssignment;
	}

	public void setComponentSystemAssignment(
			ComponentSystemAssignment componentSystemAssignment) {
		this.componentSystemAssignment = componentSystemAssignment;
	}
	
	@XmlElementWrapper(name="services-buildings")
	@XmlElement(name="structure")
	public List<SpatialContainerItem> getServicesBuildings() {
		return servicesBuildings;
	}

	public void setServicesBuildings(List<SpatialContainerItem> servicesBuildings) {
		this.servicesBuildings = servicesBuildings;
	}
	
}
