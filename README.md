## features
This is a plugin to decompress phar or plugins.  
This plugin is made for PocketMine-MP.  
- This is a plugin for developers!
- Can decompress safely without activating the plugin!
- It is possible to decompress many phar with a single command!
### unsupported features
- Asynchronous decompression is **not** supported.  
- Metadata and Stub extraction are **not** supported.  
- When PocketMine-MP.phar (other than the plugin) is decompressed with this plugin, the information necessary for starting the program in the phar will be lost, and it may not work even if the source code is recompressed.  
### usage
- First, start the server.
- Second, copy the plugins you want to decompress to the `[PocketMine-MP]/plugin_data/unphar/target` folder.
- Third, type `unphar` in the **console** and execute the command.
- After executing the command, the plugins will be decompressed into the `[PocketMine-MP]/plugin_data/unphar/output` folder.
### note
The file or directory is created if it does not exist, and is overwritten if it already exists.  
If editing the unzipped code, it is recommended to copy it to another directory.  
## console output example
```
unphar
[21:05:02.011] [Server thread/INFO]: [unphar] unphar - start
[21:05:02.014] [Server thread/INFO]: [unphar] unphar - DevTools.phar
[21:05:02.030] [Server thread/INFO]: [unphar] unphar - PocketMine-MP.phar
[21:05:03.984] [Server thread/INFO]: [unphar] unphar - exit.
```
## permission
| command | permission |
|:---:|:---:|
| unphar | Console only |
## license
| Plugin | License |
|:---:|:---:|
| [DevTools](https://github.com/pmmp/DevTools) | [LGPL-3.0 License](https://github.com/pmmp/DevTools/blob/master/LICENSE) |

The two lines of source code have been copied from the old DevTools.
