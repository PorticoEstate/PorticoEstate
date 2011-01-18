package no.bimfm.jaxb;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlAttribute;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

import no.bimfm.ifc.IfcSdaiException;

@XmlRootElement
public class MaterialItem {
	
	
	private String type = null;
	private List<MaterialLayer> materialName = null;
	private List<MaterialLayer> materialLayer = null;
	private String layerSetName = null;
	
	public MaterialItem() {
	}
	public MaterialItem(Type type, List<MaterialLayer> names) {
		if(type ==Type.SINGLE) {
			setType(Type.SINGLE);
			if(names.size()!= 1) {
				throw new IfcSdaiException("Error creating material item!");
			}
			this.materialName = new ArrayList<MaterialLayer>(names);
		} else if (type == Type.LIST) {
			setType(Type.LIST);
			if(names.size()== 0) {
				throw new IfcSdaiException("Error creating material item list!");
			}
			this.materialName = new ArrayList<MaterialLayer>(names);
		} else {
			throw new IfcSdaiException("Wrong usage of MaterialItem class!(should not be layerset)");
		}
	}
	public MaterialItem(Type type, String materialLayerName, List<MaterialLayer> names) {
		if(type ==Type.LAYERSET) {
			setType(Type.LAYERSET);
			this.layerSetName = materialLayerName;
			this.materialLayer = new ArrayList<MaterialLayer>(names);
		}else {
			throw new IfcSdaiException("Wrong usage of MaterialItem class! (should be layerset)");
		}
	}
	
	public void setType(Type type) {
		this.type = type.key;
	}
	@XmlAttribute
	public String getType() {
		return type;
	}

	public void setMaterialName(List<MaterialLayer> material) {
		this.materialName = material;
	}
	
	@XmlElement(name="name") 
	public List<MaterialLayer> getMaterialName() {
		return materialName;
	}
	@XmlElement(name="name")
	public String getLayerSetName() {
		return layerSetName;
	}
	public void setLayerSetName(String layerSetName) {
		this.layerSetName = layerSetName;
	}
	
	@XmlElementWrapper(name="layerset")
	@XmlElement(name="layer")
	public List<MaterialLayer> getMaterialLayer() {
		return materialLayer;
	}
	public void setMaterialLayer(List<MaterialLayer> materialLayer) {
		this.materialLayer = materialLayer;
	}

	public enum Type {
		SINGLE("single"),
		LIST("list"),
		LAYERSET("layerSet");
		private final String key;
		Type(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}
}
