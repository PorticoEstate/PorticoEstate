package no.bimconverter.ifc.v2x3;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.lang.A_string;
import jsdai.lang.SchemaInstance;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
import jsdai.lang.SdaiModel;
@XmlRootElement
public class ModelInformation {
	private String authorization;
	private String author;
	private String changeDate;
	private String description;
	private String organization;
	private String originatingSystem;
	private String preProcessor;
	private String valDate;
	private String nativeSchema;
	

	public ModelInformation() {
	}
	
	public ModelInformation load(SdaiModel model) {
		try {
			
			SchemaInstance currentSchema = model.getAssociatedWith().getByIndex(1);
			setNativeSchema(currentSchema.getNativeSchemaString());
			authorization = currentSchema.getAuthorization();
			author = getStringListAsString(currentSchema.getAuthor());
			changeDate = currentSchema.getChangeDate();
			description = getStringListAsString(currentSchema.getDescription());
			organization = this.getStringListAsString(currentSchema.getOrganization());
			originatingSystem = currentSchema.getOriginatingSystem();
			preProcessor = currentSchema.getPreprocessorVersion();
		} catch (SdaiException e) {
			
		}
		return this;
	}
	
	

	public String getOrganization() {
		return organization;
	}

	public void setOrganization(String organization) {
		this.organization = organization;
	}

	public String getOriginatingSystem() {
		return originatingSystem;
	}

	public void setOriginatingSystem(String originatingSystem) {
		this.originatingSystem = originatingSystem;
	}

	public String getPreProcessor() {
		return preProcessor;
	}

	public void setPreProcessor(String preProcessor) {
		this.preProcessor = preProcessor;
	}

	protected String getStringListAsString(A_string stringAggregation) throws SdaiException {
		StringBuilder resultingString = new StringBuilder();
		if(stringAggregation.getMemberCount() > 0) {
			SdaiIterator integerIterator = stringAggregation.createIterator();
			while(integerIterator.next()) {
				resultingString.append(stringAggregation.getCurrentMember(integerIterator)+";");
			}
			resultingString.deleteCharAt(resultingString.length()-1);
		}
		return resultingString.toString();
	}

	public String getAuthorization() {
		return authorization;
	}

	public void setAuthorization(String authorization) {
		this.authorization = authorization;
	}

	public String getAuthor() {
		return author;
	}

	public void setAuthor(String author) {
		this.author = author;
	}

	public String getChangeDate() {
		return changeDate;
	}

	public void setChangeDate(String changeDate) {
		this.changeDate = changeDate;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public String getValDate() {
		return valDate;
	}

	public void setValDate(String valDate) {
		this.valDate = valDate;
	}

	public void setNativeSchema(String nativeSchema) {
		this.nativeSchema = nativeSchema;
	}

	public String getNativeSchema() {
		return nativeSchema;
	}

	
	
}
