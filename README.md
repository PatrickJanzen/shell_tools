
Small tools for autocompletion in the shell written in PHP

### currently implemented functions: 
 - complete:ssh
   - reads your ~/.ssh/config for configured hosts
   - using -d option outputs a table of your config [example](./doc/example_ssh.md)
 - complete:project
   - scans the subfolders of a given dir for local git checkouts

### prerequisites:
 - php >= 8.1
 - composer

### installation:
 - checkout repo
 - run composer install
 - copy and modify example_functions file
 - source the file in your shell profile script



see [example_functions](./doc/example_functions)

tested on Mac with Oh My Zsh