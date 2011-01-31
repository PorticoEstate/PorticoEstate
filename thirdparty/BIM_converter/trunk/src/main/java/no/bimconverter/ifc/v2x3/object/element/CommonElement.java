package no.bimconverter.ifc.v2x3.object.element;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;

import no.bimconverter.ifc.jaxb.BoundaryItem;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.MaterialItem;
import no.bimconverter.ifc.jaxb.MaterialLayer;
import no.bimconverter.ifc.jaxb.SpatialContainerItem;
import no.bimconverter.ifc.v2x3.object.CommonObjectImpl;



import jsdai.SIfc2x3.AIfcmaterial;
import jsdai.SIfc2x3.AIfcmateriallayer;
import jsdai.SIfc2x3.AIfcrelassociates;
import jsdai.SIfc2x3.AIfcrelcontainedinspatialstructure;
import jsdai.SIfc2x3.AIfcreldefines;
import jsdai.SIfc2x3.AIfcrelspaceboundary;
import jsdai.SIfc2x3.EIfcbuildingstorey;
import jsdai.SIfc2x3.EIfccovering;
import jsdai.SIfc2x3.EIfccoveringtype;
import jsdai.SIfc2x3.EIfcelement;
import jsdai.SIfc2x3.EIfcmaterial;
import jsdai.SIfc2x3.EIfcmateriallayer;
import jsdai.SIfc2x3.EIfcmateriallayerset;
import jsdai.SIfc2x3.EIfcmateriallayersetusage;
import jsdai.SIfc2x3.EIfcmateriallist;
import jsdai.SIfc2x3.EIfcobject;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcrelassociates;
import jsdai.SIfc2x3.EIfcrelassociatesmaterial;
import jsdai.SIfc2x3.EIfcrelcontainedinspatialstructure;
import jsdai.SIfc2x3.EIfcreldefines;
import jsdai.SIfc2x3.EIfcreldefinesbytype;
import jsdai.SIfc2x3.EIfcrelspaceboundary;
import jsdai.SIfc2x3.EIfcroot;
import jsdai.SIfc2x3.EIfcspace;
import jsdai.SIfc2x3.EIfcspatialstructureelement;
import jsdai.SIfc2x3.EIfctypeobject;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;

public class CommonElement extends CommonObjectImpl{
	private List<MaterialItem> material = null;
	protected List<SpatialContainerItem> spatialcontainer = null;
	protected Decomposition spatialDecomposition = null;
	
	@Override
	public void load(EIfcobjectdefinition object) {
		EIfcelement element = (EIfcelement) object;
		super.load(object);
		try {
			this.loadAttributes(element);
			this.loadMaterial(element);
			this.loadClassification(element);
			this.loadBaseQuantities(element);
			this.loadProperties(element);
			this.loadBoundary(element);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	/*
	 * Will need changes for 2x4
	 */
	protected void loadMaterial(EIfcobjectdefinition object) throws SdaiException {
		AIfcrelassociates associationsAggregation = object.getHasassociations(null, null);
		SdaiIterator associationsIterator = associationsAggregation.createIterator();
		while(associationsIterator.next()) {
			EIfcrelassociates association = associationsAggregation.getCurrentMember(associationsIterator);
			if(association.isKindOf(EIfcrelassociatesmaterial.class)) {
				EEntity materialEntity = ((EIfcrelassociatesmaterial)association).getRelatingmaterial(null);
				initializeMaterial();
				List<MaterialLayer> items = new ArrayList<MaterialLayer>();
				if(materialEntity.isKindOf(EIfcmaterial.class)) {
					EIfcmaterial material = (EIfcmaterial) materialEntity;
					items.add(new MaterialLayer(material.getName(null)));
					this.material.add(new MaterialItem(MaterialItem.Type.SINGLE, items));
				} else if ( materialEntity.isKindOf(EIfcmateriallist.class)) {
					this.processMaterialList((EIfcmateriallist)materialEntity, items);
					this.material.add(new MaterialItem(MaterialItem.Type.LIST, items));
				} else if ( materialEntity.isKindOf(EIfcmateriallayersetusage.class)) {
					// ignore
				} else if ( materialEntity.isKindOf(EIfcmateriallayerset.class)) {
					EIfcmateriallayerset layerSet = (EIfcmateriallayerset) materialEntity;
					String layerSetName = (layerSet.testLayersetname(null)) ? layerSet.getLayersetname(null) : null;
					this.processMaterialLayerSet(layerSet, items);
					this.material.add(new MaterialItem(MaterialItem.Type.LAYERSET, layerSetName, items));
				} else if ( materialEntity.isKindOf(EIfcmateriallayer.class)) {
					//ignore, these will be referenced from the layerset
				}
				
			}
		}
	}


	private void processMaterialLayerSet(EIfcmateriallayerset materialEntity,List<MaterialLayer> items) throws SdaiException {
		AIfcmateriallayer layerSet = materialEntity.getMateriallayers(null);
		SdaiIterator layersIterator = layerSet.createIterator();
		while(layersIterator.next()) {
			EIfcmateriallayer layer = layerSet.getCurrentMember(layersIterator);
			MaterialLayer mLayer = new MaterialLayer();
			if(layer.testMaterial(null)) {
				mLayer.name = layer.getMaterial(null).getName(null);
			}
			mLayer.thickness = String.valueOf(layer.getLayerthickness(null));
			if(layer.testIsventilated(null)) {
				int result = layer.getIsventilated(null);
				switch (result){
					case 1:
						mLayer.ventilated = "true";
						break;
					case 2:
						mLayer.ventilated = "false";
						break;
					default:
						mLayer.ventilated = "undefined";
				}
			}
			items.add(mLayer);
		}
		
	}


	private void processMaterialList(EIfcmateriallist materialEntity, List<MaterialLayer> items) throws SdaiException {
		AIfcmaterial materials = materialEntity.getMaterials(null);
		SdaiIterator materialsIterator  =materials.createIterator();
		while(materialsIterator.next()) {
			EIfcmaterial material = materials.getCurrentMember(materialsIterator);
			items.add(new MaterialLayer(material.getName(null)));
		}
		
	}


	private void initializeMaterial() {
		if(this.material == null) {
			this.material = new ArrayList<MaterialItem>();
		}
		
	}

	@XmlElementWrapper(name="materials")
	@XmlElement(name="material")
	public List<MaterialItem> getMaterial() {
		return material;
	}


	public void setMaterial(List<MaterialItem> material) {
		this.material = material;
	}
	protected void loadParentItemsIntoSpatialContainer(EIfcelement entity, Class<? extends EIfcobjectdefinition> classType) throws SdaiException {
		AIfcrelcontainedinspatialstructure containedInAggregate = entity.getContainedinstructure(null, null);
		
		SdaiIterator containedInIterator = containedInAggregate.createIterator();
		
		while(containedInIterator.next()) {
			
			
			EIfcrelcontainedinspatialstructure relContSpatial = containedInAggregate.getCurrentMember(containedInIterator);
			EIfcspatialstructureelement relatingStructure = relContSpatial.getRelatingstructure(null);
			
			if(relatingStructure.isKindOf(EIfcbuildingstorey.class)) {
				initializeSpatialDecomposition();
				this.spatialDecomposition.addBuildingStoreyId(relatingStructure.getGlobalid(null));
			}
			if(relatingStructure.isKindOf(EIfcspace.class)) {
				initializeSpatialDecomposition();
				this.spatialDecomposition.addSpaceId(relatingStructure.getGlobalid(null));
			}
		}
	}
	
	private void initializeSpatialDecomposition() {
		if(this.spatialDecomposition == null) {
			this.spatialDecomposition = new Decomposition();
		}
	}
	
	@XmlElementWrapper(name="spatialContainer")
	@XmlElement(name="item") 
	public List<SpatialContainerItem> getSpatialcontainer() {
		return spatialcontainer;
	}
	public void setSpatialcontainer(List<SpatialContainerItem> spatialcontainer) {
		this.spatialcontainer = spatialcontainer;
	}
	protected EIfctypeobject getTypeObject(EIfcelement element) throws SdaiException {
		AIfcreldefines coveringDefinitionRelations = element.getIsdefinedby(null, null);
		SdaiIterator coveringDefinitionRelationsIterator = coveringDefinitionRelations.createIterator();
		while(coveringDefinitionRelationsIterator.next()) {
			EIfcreldefines definesRelation = coveringDefinitionRelations.getCurrentMember(coveringDefinitionRelationsIterator);
			if(definesRelation.isKindOf(EIfcreldefinesbytype.class)) {
				EIfctypeobject typeObject = ((EIfcreldefinesbytype) definesRelation).getRelatingtype(null);
				/*
				if(typeObject.isKindOf(EIfccoveringtype.class)) {
					this.coveringType = new CoveringType();
					this.coveringType.load((EIfccoveringtype) typeObject);
					
				}
				*/
				return typeObject;
			}
		}
		return null;
	}


	protected void loadBoundary(EIfcelement windowEntity) throws SdaiException {
		AIfcrelspaceboundary boundary = windowEntity.getProvidesboundaries(null, null);
		SdaiIterator elementsIterator = boundary.createIterator();
		BoundaryItem sbItem = null;
		while(elementsIterator.next()) {
			EIfcrelspaceboundary rel = boundary.getCurrentMember(elementsIterator);
			sbItem = populateSpaceBoundaryItem(rel, rel.getRelatingspace(null));
			if(sbItem != null) {
				super.initializeSpaceBoundary();
				this.spaceBoundary.add(sbItem);
			}
		}
	}

	public Decomposition getSpatialDecomposition() {
		return spatialDecomposition;
	}

	public void setSpatialDecomposition(Decomposition spatialDecomposition) {
		this.spatialDecomposition = spatialDecomposition;
	}
}
