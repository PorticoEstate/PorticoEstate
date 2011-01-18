package no.bimfm.ifc.v2x3.owner;

import java.util.List;

import javax.xml.bind.annotation.XmlAttribute;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

import no.bimfm.ifc.v2x3.object.CommonObjectDefinition;
import no.bimfm.jaxb.NameValuePair;
import jsdai.SIfc2x3.EIfcaddress;
import jsdai.SIfc2x3.EIfcaddresstypeenum;
import jsdai.SIfc2x3.EIfcpostaladdress;
import jsdai.SIfc2x3.EIfctelecomaddress;
import jsdai.lang.SdaiException;

/*
 * IfcAddress container class
 * using IFC 2x3
 */
@XmlRootElement
public class Address extends CommonObjectDefinition{
	
	private String purpose;
	private String description;
	private String userDefinedPurpose;
	private List<NameValuePair> addressInformation;
	private String ifcAddressType;
	//Ifcpostaladdress variables
	private String internalLocation;
	private String addressLines;
	private String postalBox;
	private String town;
	private String region;
	private String postalCode;
	private String country;
	//Ifctelecomaddress variables
	
	private String telephoneNumbers;//	 : 	OPTIONAL LIST [1:?] OF IfcLabel;
	private String facsimileNumbers;//	 : 	OPTIONAL LIST [1:?] OF IfcLabel;
	private String pagerNumber;//	 : 	OPTIONAL IfcLabel;
	private String electronicMailAddresses;//	 : 	OPTIONAL LIST [1:?] OF IfcLabel;
	private String wwwHomePageURL;//	 : 	OPTIONAL IfcLabel;
	
	public Address() {
	}
	
	public Address load(EIfcaddress address) {
		try {
			if(address.testPurpose(null)) {
				this.purpose = EIfcaddresstypeenum.toString(address.getPurpose(null));
			}
			if(address.testDescription(null)) {
				this.description = address.getDescription(null);
			}
			if(address.testUserdefinedpurpose(null)) {
				this.userDefinedPurpose = address.getUserdefinedpurpose(null);
			}
			if(address.isKindOf(EIfcpostaladdress.class)) {
				EIfcpostaladdress postalAddress = (EIfcpostaladdress)address;
				extractPostalAddressValues(postalAddress);
				/*
				this.addressInformation = new ArrayList<NameValuePair>();
				this.addressInformation.add(new NameValuePair("addressType", address.getInstanceType().getName(null)));
				this.addressInformation.addAll(extractSingleValueAttributes(postalAddress));
				*/
				
			} else if ( address.isKindOf(EIfctelecomaddress.class)) {
				EIfctelecomaddress telecomAddress = (EIfctelecomaddress)address;
				this.ifcAddressType = telecomAddress.getInstanceType().getName(null);
				if(telecomAddress.testTelephonenumbers(null)) {
					this.telephoneNumbers = super.getStringListAsString(telecomAddress.getTelephonenumbers(null));
				}
				if(telecomAddress.testFacsimilenumbers(null)) {
					this.facsimileNumbers = super.getStringListAsString(telecomAddress.getFacsimilenumbers(null));
				}
				if(telecomAddress.testPagernumber(null)) {
					this.pagerNumber = telecomAddress.getPagernumber(null);
				}
				if( telecomAddress.testElectronicmailaddresses(null)) {
					this.electronicMailAddresses = super.getStringListAsString(telecomAddress.getElectronicmailaddresses(null));
				}
				if( telecomAddress.testWwwhomepageurl(null)) {
					this.wwwHomePageURL = telecomAddress.getWwwhomepageurl(null);
				}
				/*
				this.addressInformation = new ArrayList<NameValuePair>();
				this.addressInformation.add(new NameValuePair("addressType", address.getInstanceType().getName(null)));
				this.addressInformation.addAll(extractSingleValueAttributes(telecomAddress));
				*/
			}
		} catch (SdaiException e) {
			e.printStackTrace();
		}
		return this;
	}

	private void extractPostalAddressValues(EIfcpostaladdress postalAddress)
			throws SdaiException {
		this.ifcAddressType = postalAddress.getInstanceType().getName(null);
		if(postalAddress.testInternallocation(null)) {
			this.internalLocation = postalAddress.getInternallocation(null);
		}
		if(postalAddress.testAddresslines(null)) {
			this.addressLines = super.getStringListAsString(postalAddress.getAddresslines(null));
		}
		if(postalAddress.testPostalbox(null)) {
			this.postalBox = postalAddress.getPostalbox(null);
		}
		if(postalAddress.testTown(null)) {
			this.town = postalAddress.getTown(null);
		}
		if(postalAddress.testRegion(null)) {
			this.region = postalAddress.getRegion(null);
		}
		if(postalAddress.testPostalcode(null)) {
			this.postalCode = postalAddress.getPostalcode(null);
		}
		if(postalAddress.testCountry(null)) {
			this.country = postalAddress.getCountry(null);
		}
	}

	public String getPurpose() {
		return purpose;
	}

	public String getDescription() {
		return description;
	}

	public String getUserDefinedPurpose() {
		return userDefinedPurpose;
	}
	@XmlElementWrapper(name="addressInformation")
	@XmlElement(name="item") 
	public List<NameValuePair> getAddressInformation() {
		return addressInformation;
	}

	public String getInternalLocation() {
		return internalLocation;
	}

	public void setInternalLocation(String internalLocation) {
		this.internalLocation = internalLocation;
	}

	public String getAddressLines() {
		return addressLines;
	}

	public void setAddressLines(String addressLines) {
		this.addressLines = addressLines;
	}

	public String getPostalBox() {
		return postalBox;
	}

	public void setPostalBox(String postalBox) {
		this.postalBox = postalBox;
	}

	public String getTown() {
		return town;
	}

	public void setTown(String town) {
		this.town = town;
	}

	public String getRegion() {
		return region;
	}

	public void setRegion(String region) {
		this.region = region;
	}

	public String getPostalCode() {
		return postalCode;
	}

	public void setPostalCode(String postalCode) {
		this.postalCode = postalCode;
	}

	public String getCountry() {
		return country;
	}

	public void setCountry(String country) {
		this.country = country;
	}

	public String getTelephoneNumbers() {
		return telephoneNumbers;
	}

	public void setTelephoneNumbers(String telephoneNumbers) {
		this.telephoneNumbers = telephoneNumbers;
	}

	public String getFacsimileNumbers() {
		return facsimileNumbers;
	}

	public void setFacsimileNumbers(String facsimileNumbers) {
		this.facsimileNumbers = facsimileNumbers;
	}

	public String getPagerNumber() {
		return pagerNumber;
	}

	public void setPagerNumber(String pagerNumber) {
		this.pagerNumber = pagerNumber;
	}

	public String getElectronicMailAddresses() {
		return electronicMailAddresses;
	}

	public void setElectronicMailAddresses(String electronicMailAddresses) {
		this.electronicMailAddresses = electronicMailAddresses;
	}

	public String getWWWHomePageURL() {
		return wwwHomePageURL;
	}

	public void setWWWHomePageURL(String wWWHomePageURL) {
		wwwHomePageURL = wWWHomePageURL;
	}
	
	public void setPurpose(String purpose) {
		this.purpose = purpose;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public void setUserDefinedPurpose(String userDefinedPurpose) {
		this.userDefinedPurpose = userDefinedPurpose;
	}

	public void setAddressInformation(List<NameValuePair> addressInformation) {
		this.addressInformation = addressInformation;
	}
	@XmlAttribute
	public String getIfcAddressType() {
		return ifcAddressType;
	}

	public void setIfcAddressType(String ifcAddressType) {
		this.ifcAddressType = ifcAddressType;
	}

	@Override
	public String toString() {
		return "Address [purpose=" + purpose + ", description=" + description
				+ ", userDefinedPurpose=" + userDefinedPurpose
				+ ", addressInformation=" + addressInformation + "]";
	}
}
