package no.bimconverter.ifc;

import java.io.IOException;
import java.io.InputStream;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import no.bimconverter.ifc.v2x3.IfcModelImpl;
import no.bimconverter.ifc.v2x3.object.CommonObjectDefinition;

public class IfcTestMethods {
	protected String testingRepository = "FMHandoverRepository";
	protected String nonExistingRepository = "dummmmmyRepoThatDoesNotExist";
	protected String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	protected IfcModelImpl model;
	protected Repositories repo = null;
	public IfcTestMethods() {
	}
	
	protected void createTestRepo() {
		model = new IfcModelImpl(testingRepository);
		String ifcFilename = getClass().getResource( "/" +testIfcFileName ).toString();
		repo = new RepositoriesImpl();
		InputStream ifcFileStream = getClass().getResourceAsStream("/"+testIfcFileName);
		if(model.checkIfRepositoryExists(testingRepository)) {
			model.deleteRepository(testingRepository);
		}
		model.importRepository(testingRepository, ifcFileStream);
		try {
			ifcFileStream.close();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	/*
	 * TODO: chech if the class argument has an @XmlRootElement annotation
	 */
	protected void outputXmlToSystemOut(Object jaxbObject) {
		
		JAXBContext jc;
		try {
			jc = JAXBContext.newInstance(jaxbObject.getClass());
			Marshaller m = jc.createMarshaller();
			m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
			m.marshal( jaxbObject, System.out );
		} catch (JAXBException e) {
			e.printStackTrace();
		}
		
	}
}
