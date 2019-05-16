# Lúo

Lúo is an expert system with the purpose of being a web solution for inference questions returning the expected answer at the end.

The project is requires a database to work and currently doesn't have a GUI for creating the rules/logic.

You can test a version [here](http://www.mghenschel.com.br/projects/furb/luo/login) (available only in Portuguese - Brazil).

## Run

To run the system locally, just download the repository and run `docker-compose up -d` on the root folder.

Lúo will be available in [localhost:5000](http://localhost:5000).

If you've never ran it before, access [localhost:8080](http://localhost:8080) with the credentials in the `.env` file and import the `luo.sql` file, available in the root folder of the project.

## Future

### To do

- Start with question
- Import
- Author page
- Avoid loop (generate node of rules that are above in the tree)
- Edit System
  - Create variable
  - Save variable
  - Remove variable
  - Create option
  - Save/Remove option
  - Create rule
  - Reorder rule
  - Remove rule
  - Create condition (value)
  - Create condition (logic)
  - Save/Remove condition
  - Create consequence
  - Save/Remove consequence

### Ideas

- Password protected system
- Interface to create system
- Add _else_ option
- Suggest shortest way (or let person decide if wants to order, ask less questions, etc...)
- Alert for inconsistencies
- Allow float fields
- Compare fields
- Regular Expression for String
- Multiple? Numeric intervals

-----

Lúo was built by @mghextreme and @evandroMSchmitz as a college project for AI classes.
