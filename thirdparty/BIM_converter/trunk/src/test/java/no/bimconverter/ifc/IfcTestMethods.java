package no.bimconverter.ifc;

import java.io.IOException;
import java.io.InputStream;

import no.bimconverter.ifc.v2x3.IfcModelImpl;

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
}
