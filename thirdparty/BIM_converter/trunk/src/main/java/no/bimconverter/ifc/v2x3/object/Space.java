package no.bimconverter.ifc.v2x3.object;

import java.util.List;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.IfcSdaiException;
import no.bimconverter.ifc.jaxb.BoundaryItem;
import no.bimconverter.ifc.jaxb.SpaceSpatialContainer;
import no.bimconverter.ifc.jaxb.SpatialContainerItem;


import jsdai.SIfc2x3.AIfcproduct;
import jsdai.SIfc2x3.AIfcrelcontainedinspatialstructure;
import jsdai.SIfc2x3.AIfcrelspaceboundary;
import jsdai.SIfc2x3.EIfccovering;
import jsdai.SIfc2x3.EIfcdoor;
import jsdai.SIfc2x3.EIfcelement;
import jsdai.SIfc2x3.EIfcinternalorexternalenum;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcproduct;
import jsdai.SIfc2x3.EIfcrelcontainedinspatialstructure;
import jsdai.SIfc2x3.EIfcrelspaceboundary;
import jsdai.SIfc2x3.EIfcroot;
import jsdai.SIfc2x3.EIfcspace;
import jsdai.SIfc2x3.EIfcwindow;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;

@XmlRootElement
public class Space extends SpatialStructure{
	final private static String commonPropertyName = "Pset_SpaceCommon";
	private SpaceSpatialContainer spatialContainer = null;
	
	@Override
	public void load(EIfcobjectdefinition objectEntity) {
		super.load(objectEntity);
		EIfcspace spaceEntity = (EIfcspace)objectEntity;
		try {
			this.loadAttributes(spaceEntity);
			this.loadClassification(spaceEntity);
			this.loadBaseQuantities(spaceEntity);
			this.loadProperties(spaceEntity);
			this.loadSpatialDecomposition(spaceEntity);
			this.loadSpatialContainer(spaceEntity);
			this.loadBoundary(spaceEntity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	private void loadBoundary(EIfcspace spaceEntity)  throws SdaiException {
		
		AIfcrelspaceboundary elements = spaceEntity.getBoundedby(null, null);
		SdaiIterator elementsIterator = elements.createIterator();
		BoundaryItem sbItem = null;
		while(elementsIterator.next()) {
			EIfcrelspaceboundary rel = elements.getCurrentMember(elementsIterator);
			if(rel.testRelatedbuildingelement(null)) {
				EIfcelement buildingElement = rel.getRelatedbuildingelement(null);
				sbItem = populateSpaceBoundaryItem(rel, buildingElement);
				if(sbItem != null) {
					initializeSpaceBoundary();
					this.spaceBoundary.add(sbItem);
				}
			}
			
		}
	}

	

	private void loadSpatialContainer(EIfcspace spaceEntity) throws SdaiException {
		AIfcrelcontainedinspatialstructure elements = spaceEntity.getContainselements(null, null);
		SdaiIterator elementsIterator = elements.createIterator();
		while(elementsIterator.next()) {
			EIfcrelcontainedinspatialstructure rel = elements.getCurrentMember(elementsIterator);
			AIfcproduct relatedElements = rel.getRelatedelements(null);
			if(relatedElements.getMemberCount() > 0) {
				initializeSpatialContainer();
			}
			SdaiIterator productIterator = relatedElements.createIterator();
			SpatialContainerItem spatialContainerItem = null;
			while(productIterator.next()) {
				EIfcproduct product = relatedElements.getCurrentMember(productIterator);
				if(product.isKindOf(EIfccovering.class)) {
					spatialContainerItem = populateSpatialContainerItem(product, "IfcCovering");
					//this.spatialContainer.getCoverings().add(spatialContainerItem);
					this.spatialContainer.addCovering(spatialContainerItem);
				} else if(product.isKindOf(EIfcdoor.class)) {
					spatialContainerItem = populateSpatialContainerItem(product, "IfcDoor");
					//this.spatialContainer.getDoorsWindows().add(spatialContainerItem);
					this.spatialContainer.addDoorWindow(spatialContainerItem);
				} else if(product.isKindOf(EIfcwindow.class)) {
					spatialContainerItem = populateSpatialContainerItem(product, "IfcWindow");
					//this.spatialContainer.getDoorsWindows().add(spatialContainerItem);
					this.spatialContainer.addDoorWindow(spatialContainerItem);
				} else if(product.isKindOf(EIfcelement.class)) {
					String type = product.getInstanceType().getName(null);
					spatialContainerItem = populateSpatialContainerItem(product, type);
					//this.spatialContainer.getElements().add(spatialContainerItem);
					this.spatialContainer.addElement(spatialContainerItem);
				}
			}
			
		}
		
	}

	private SpatialContainerItem populateSpatialContainerItem(EIfcproduct product, String type) throws SdaiException {
		SpatialContainerItem item = new SpatialContainerItem();
		item.guid = product.getGlobalid(null);
		item.type = type;
		if(product.testName(null)) {
			item.name = product.getName(null);
		}
		if(product.testDescription(null)) {
			item.description = product.getDescription(null);
		}
		return item;
	}

	private void initializeSpatialContainer() {
		if(this.spatialContainer == null) {
			this.spatialContainer = new SpaceSpatialContainer();
		}
		
	}

	private void loadAttributes(EIfcspace entity) throws SdaiException {
		
		if(entity.testInteriororexteriorspace(null)) {
			int interiorOrExterior = entity.getInteriororexteriorspace(null);
			//this.attributes.put(Attribute.INTERNAL_EXTERNAL.key, EIfcinternalorexternalenum.toString(interiorOrExterior));
			this.attributes.setInternalExternal(EIfcinternalorexternalenum.toString(interiorOrExterior));
		}
	}
	
	private void loadProperties(EIfcspace space) throws SdaiException {
		if(this.propertiesList == null) {
			this.relateObjectPropertiesAndQuantities(space);
		}
		super.loadProperties(space);
		super.setCommonProperty(Space.commonPropertyName);
	}
	
	private void loadSpatialDecomposition(EIfcspace space) throws SdaiException {
		insertParentIds(space);
		
	}
	public void insertParentIds(EIfcspace space) throws SdaiException {
		List<EEntity> parents = this.getParentEntities(space);
		if( parents.size() > 0) {
			this.spatialDecomposition.addBuildingStoreyId(((EIfcroot) parents.get(0)).getGlobalid(null));
			
		} else {
			throw new IfcSdaiException("Error with parent element structure");
		}
	}
	public enum Attribute {
		INTERNAL_EXTERNAL("Interior / Exterior");
		private final String key;
		Attribute(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}
	public SpaceSpatialContainer getSpatialContainer() {
		return spatialContainer;
	}

	public void setSpatialContainer(SpaceSpatialContainer spatialContainer) {
		this.spatialContainer = spatialContainer;
	}
	
}
